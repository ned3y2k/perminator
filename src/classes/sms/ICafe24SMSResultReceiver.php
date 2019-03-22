<?php
namespace classes\sms;

interface ICafe24SMSResultReceiver {
	public function onReceiveCafe24SMSResult($type, $status, $receiver, $comment);
}