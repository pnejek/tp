<?php

require_once dirname(dirname(__FILE__)).'/model/banners/banners.class.php';
$banners = new banners($modx);
return $banners->initialize('mgr');