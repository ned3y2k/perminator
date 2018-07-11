<?php
namespace classes\data\page;

abstract class Paginator {
	private $prepared = false;

	private $currentPage;
	private $itemLimit;
	private $itemCount;
	private $pageBlock = 0;
	private $pagingLimit;
	private $pageCount = 0;

	private $begin;
	private $end;

	/**
	 * 페이지 카운트 수
	 * @throws \LogicException
	 * @return number
	 */
	public function getPageCount() {
		if (!$this->prepared) throw new \LogicException('먼저 prepare 가 되어 있어야 합니다.');

		return $this->pageCount;
	}

	/**
	 * 최대 페이징수
	 * @throws \LogicException
	 * @return number
	 */
	public function getPagingLimit() {
		if (!$this->prepared) throw new \LogicException('먼저 prepare 가 되어 있어야 합니다.');

		return $this->pagingLimit;
	}

	/**
	 * 보여줄 아이템수
	 * @throws \LogicException
	 * @return number
	 */
	public function getSize() {
		if (!$this->prepared) throw new \LogicException('먼저 prepare 가 되어 있어야 합니다.');

		return $this->itemCount;
	}

	/**
	 * 아이템 제한수
	 * @throws \LogicException
	 * @return number
	 */
	public function getItemLimit() {
		if (!$this->prepared) throw new \LogicException('먼저 prepare 가 되어 있어야 합니다.');

		return $this->itemLimit;
	}

	/**
	 * 현재 페이지
	 * @throws \LogicException
	 * @return number
	 */
	public function getPage() {
		if (!$this->prepared) throw new \LogicException('먼저 prepare 가 되어 있어야 합니다.');

		return $this->currentPage;
	}

	/**
	 * 페이징 시작 번호
	 * @throws \LogicException
	 * @return number
	 */
	public function getBegin() {
		if (!$this->prepared) throw new \LogicException('먼저 prepare 가 되어 있어야 합니다.');

		return $this->begin;
	}

	/**
	 * 페이징 끝 번호
	 * @throws \LogicException
	 * @return number
	 */
	public function getEnd() {
		if (!$this->prepared) throw new \LogicException('먼저 prepare 가 되어 있어야 합니다.');

		return $this->end;
	}

	/**
	 * @return int|number
	 */
	public function getPrevPage() {
		$page = $this->getPage() - $this->pagingLimit;

		return $page < 1 ? 1 : $page;
	}

	/**
	 * @return number
	 */
	public function getNextPage() {
		$page = $this->getPage() + $this->pagingLimit;

		return $page > $this->getPageCount() ? $this->getPageCount() : $page;
	}

	/**
	 * @param number     $currentPage
	 * @param number     $itemCount
	 * @param int|number $itemLimit
	 * @param int|number $pagingLimit
	 *
	 * @throws \Exception
	 * @throws \InvalidArgumentException
	 */
	public function prepare($currentPage, $itemCount, $itemLimit = 15, $pagingLimit = 10) {
		$this->prepared = true;
		if (!is_numeric($itemCount)) throw new \InvalidArgumentException("Invalid ItemCount");
		if (!is_numeric($pagingLimit)) throw new \InvalidArgumentException("Invalid PagingLimit");
		$this->itemCount = $itemCount;
        $this->itemLimit = $itemLimit;

		try {
			$this->pagingLimit = $pagingLimit;

			$pageCount = $this->pageCount = floatval(ceil($itemCount / $itemLimit));
			// $itemCount = $this->itemCountSetAfterValidate ( $itemCount );
			$currentPage = $this->currentPageSetAfterValidate($currentPage);
			$pageBlock   = $this->pageBlock = floor(($this->currentPage - 1) / $pagingLimit);
			// $itemLimit = $this->itemLimitSetAfterValidate ( $itemLimit );

			$startPageNo = ($pageBlock * $pagingLimit) + 1;
			$startPageNo = $startPageNo < $pageCount ? $startPageNo : $pageCount;

			$endPageNo = ($startPageNo - 1) + $pagingLimit;
			$endPageNo = $endPageNo < $pageCount ? $endPageNo : $pageCount;

			if ($endPageNo == $currentPage) {
				$startPageNo = ceil($endPageNo - ($pagingLimit / 2));
				$startPageNo = $startPageNo < $pageCount ? $startPageNo : $pageCount;

				$endPageNo = ($startPageNo - 1) + $pagingLimit;
			} elseif ($startPageNo == $currentPage) {
				$startPageNo = ceil($startPageNo - ($pagingLimit / 2));
				$startPageNo = $startPageNo < $pageCount ? $startPageNo : $pageCount;
			}
			$endPageNo   = $endPageNo < $pageCount ? $endPageNo : $pageCount;
			$startPageNo = $startPageNo < 1 ? 1 : $startPageNo;


			$this->begin = $startPageNo;
			$this->end   = floatval($endPageNo);
		} catch (\InvalidArgumentException $ex) {
			throw $ex;
		}
	}

	private function currentPageSetAfterValidate($currentPage) {
		$currentPage = floatval($currentPage);

		if ($currentPage <= 0)
			$currentPage = 1;
		$this->currentPage = $currentPage;

		return $currentPage;
	}
}