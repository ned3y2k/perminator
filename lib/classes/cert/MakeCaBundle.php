<?php
# ***************************************************************************
# This PHP script creates a fresh ca-bundle.crt file for use with libcurl.
# It downloads certdata.txt from Mozilla's source tree (see URL below),
# then parses certdata.txt and extracts CA Root Certificates into PEM format.
# This PHP script works on almost any platform since its only external
# dependency is the OpenSSL commandline tool for optional text listing.
# Successfully tested with PHP 4.3.11 and later versions.
# Hacked by Guenter Knauf. ned3y2k
# ***************************************************************************
# * $Id: $
# ***************************************************************************
#
namespace classes\cert;

use classes\net\WebContentUtil;

class MakeCaBundle {
	private $url     = '';
	private $openssl = 'openssl';

	public static function getInstance() {
	    static $instance = null;
	    if($instance == null)
	        $instance = new self();
	    return $instance;
	}

	/**
	 * MakeCaBundle constructor.
	 *
	 * @param string $url
	 * @param string $openssl If the OpenSSL commandline is not in search path you can configure it here!
	 */
	public function __construct($url = 'http://lxr.mozilla.org/seamonkey/source/security/nss/lib/ckfw/builtins/certdata.txt?raw=1', $openssl = 'openssl') {
		$this->url     = $url;
		$this->openssl = $openssl;
	}

	public function getPem() {
		$currentdate    = gmdate("D M d, Y H:i:s T");
		$crtlines[]     = "##
## ca-bundle.crt -- Bundle of CA Root Certificates
##
## Converted at: $currentdate
##
## This is a bundle of X.509 certificates of public Certificate Authorities
## (CA). These were automatically extracted from Mozilla's root certificates
## file (certdata.txt).  This file can be found in the mozilla source tree:
## '/mozilla/security/nss/lib/ckfw/builtins/certdata.txt'
##
## It contains the certificates in plain text and PEM format and therefore
## can be directly used with curl / libcurl / php_curl, or with
## an Apache+mod_ssl webserver for SSL client authentication.
## Just configure this file as the SSLCACertificateFile.
##
";
		$data           = '';
		$certnum        = 0;
		$inside_cert    = false;
		$inside_license = false;
		foreach (self::readCertData() as $line) {
			$line .= "\n";

			if (strpos($line, "***** BEGIN LICENSE BLOCK *****")) {
				$inside_license = true;
			}
			if ($inside_license) {
				$crtlines[] = $line;
				if (isset($opt['l']))
					print $line;
			}
			if (strpos($line, "***** END LICENSE BLOCK *****")) {
				$inside_license = false;
			}
			if (preg_match('@^#|^\s*$"@', $line)) {
			}
			if (preg_match('@^CVS_ID\s+\"(.*)\"@', $line, $matches)) {
				$crtlines[] = "# " . $matches[1] . "\n";
			}
			if (preg_match('@^CKA_LABEL\s+[A-Z0-9]+\s+\"(.*)\"@', $line, $matches)) {
				$caname = $matches[1];
			}
			if ($inside_cert) {
				$octets = explode("\\", rtrim($line));
				array_shift($octets);
				foreach ($octets as $octet) {
					$data .= chr(octdec($octet));
				}
			}
			if (!strcasecmp($line, "CKA_VALUE MULTILINE_OCTAL\n")) {
				$inside_cert = true;
			}
			if ($inside_cert && !strcasecmp($line, "END\n")) {
				$inside_cert = false;
				if (isset($opt['v']))
					print "Parsing: $caname\n";
				$crtlines[] = "\n";
				$crtlines[] = $caname . "\n";
				$crtlines[] = str_repeat("=", strlen($caname)) . "\n";
				$pem        = "-----BEGIN CERTIFICATE-----\n"
					. chunk_split(base64_encode($data))
					. "-----END CERTIFICATE-----\n";
				$data       = '';
				if (!isset($opt['t'])) {
					$crtlines[] = $pem;
				} else {
					$descriptorspec = array(
						0 => array("pipe", "r"),
						1 => array("pipe", "w")
					);
					$cmd            = "{$this->openssl} x509 -md5 -fingerprint -text -inform PEM";
					$process        = proc_open($cmd, $descriptorspec, $pipes);
					if (is_resource($process)) {
						fwrite($pipes[0], $pem);
						fclose($pipes[0]);
						while (!feof($pipes[1])) {
							$crtlines[] = fgets($pipes[1], 1024);
						}
						fclose($pipes[1]);
						$return_value = proc_close($process);
					} else {
						die("pipe to openssl commandline failed!\n");
					}
				}
				$certnum++;
			}
		}

		return implode('', $crtlines);
	}

	private function readCertData() {
		$lines = explode("\n", WebContentUtil::read($this->url));

		return $lines;
	}
}