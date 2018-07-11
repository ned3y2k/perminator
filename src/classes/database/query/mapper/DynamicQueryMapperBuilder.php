<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 9. 14
 * 시간: 오후 10:37
 */
namespace classes\database\query\mapper;

use classes\database\query\mapper\exception\DynamicQueryExecuteInvalidArgumentException;
use classes\database\query\mapper\exception\DynamicQueryMapperBuilderException;
use classes\database\query\mapper\parser\IXmlParser;
use classes\io\exception\FileNotFoundException;

/**
 * http://www.phpliveregex.com/
 * Class DynamicQueryMapperBuilder
 *
 * @package classes\database\query\mapper
 */
class DynamicQueryMapperBuilder {
	/** 파일로 부터 */
	const TYPE_FILE = 0;
	/** 문자열로 부터 */
    const TYPE_STRING = 1;
	/** XML 파서 클래스 */
	//const PARSER = '\classes\database\query\mapper\parser\DomDocumentParser';
	const PARSER = '\classes\database\query\mapper\parser\XmlParser';
	//const PARSER = '\classes\database\query\mapper\parser\SimpleXmlParser';

	/**
	 * @param string $content
	 * @param int $type
	 * @throws DynamicQueryMapperBuilderException
	 * @return node\QueryNodeMapper
	 */
    public static function build($content, $type = self::TYPE_FILE) {
        $cacheManager = DynamicQueryContext::getCacheManager();
	    $parserName = self::PARSER;

        if($type == self::TYPE_FILE) {
	        try {
		        $content = self::findXmlPath($content);
	        } catch (\Exception $ex) {
		        throw new DynamicQueryMapperBuilderException($ex->getMessage(), $ex->getCode(), $ex);
	        }

            $id = __CLASS__.'-f-'.$content;

            $fileTime = strval(filemtime($content));

            $mapper = $cacheManager->get($id, $fileTime);
            if(!$mapper) {
	            /** @var IXmlParser $parser */
                $parser = new $parserName(file_get_contents($content));
                $mapper = $parser->getMapper();
	            if(!$mapper)
		            throw new DynamicQueryExecuteInvalidArgumentException("{$content} is empty.");
                $cacheManager->put($id, $mapper, 0, $fileTime);
            }

            return $mapper;
        } elseif(self::TYPE_STRING) {
	        if(!$content)
		        throw new DynamicQueryExecuteInvalidArgumentException("Mapper Content not Provided");

            $id = __CLASS__.'-s-'.md5($content);

            $mapper = $cacheManager->get($id);
            if($mapper == null) {
	            /** @var IXmlParser $parser */
                $parser = new $parserName($content);
                $mapper = $parser->getMapper();
                $cacheManager->put($id, $mapper);
            }

            return $mapper;
        }

        throw new \InvalidArgumentException();
    }

	/**
	 * @param string $path
	 * @return string
	 * @throws FileNotFoundException
	 */
	private static function findXmlPath($path) {
        if(file_exists($path)) {
            return $path;
        } elseif(file_exists($result = _APP_ROOT_.$path)) {
            return $result;
        }

        throw new FileNotFoundException($path);
    }
}