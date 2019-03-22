<?php
namespace classes\sms;
interface SMSSender { public function send($receiverNo, $senderNo, $message); }