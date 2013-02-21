<?php
namespace component\gnuboard\repository;

interface IMemberRepository {
	function selectById($userId);
}