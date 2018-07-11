<?php
/**
 * BITMOBILE
 * 작성자: Kyeongdae
 * 일자: 14. 7. 11
 * 시간: 오전 10:38
 */

namespace classes\web\mvc;

interface IPageBuilderPreAction {
	public function execute(PageBuilder $pageBuilder);
}