<?php

  function httpPost($postUrl, $data='')
  {
    $ch = curl_init();
    // 设置URL和相应的选项
    curl_setopt($ch, CURLOPT_URL, $postUrl);
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($data) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
  }

  function sendHttpRequest($url, $data='')
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "$url");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    if ($data) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
    echo 'Errno'.curl_error($ch);
    }
    curl_close($ch);
    return $tmpInfo;
  }

  function sendHttpGet($url, $data='')
  { 
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$url");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    if ($data) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
    echo 'Errno'.curl_error($ch);
    }
    curl_close($ch);
    return $tmpInfo;
  }

	function getNonceStr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
	}

  function getCardNo($wticket)
  {
    $postData = array(
          'args' => array
                        (
                            'wticket' => $wticket,
                            'fields'  => array('xibeiOpenid', 'xibeiCardNo')
                        ),
                'client' => CRM_CLIENT,
                'format' => 'JSON',
                'method' => 'weixin.getUser',
                'ts' => time(),
                'ver' => '1.0'
    );
    $sig = md5(CRM_CLIENT.$postData['ts'].CRM_SECRET);
    $postData['sig'] = $sig;
    $postUrl = 'http://open.life.qq.com/weixinapi.php';
    $queryStr = http_build_query($postData);

    $ch = curl_init();
    // 设置URL和相应的选项
    curl_setopt($ch, CURLOPT_URL, $postUrl);
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $queryStr);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $result = curl_exec($ch);
    curl_close($ch);
    $ret = json_decode($result, true);
    return $ret['result']['xibeiCardNo'];
  }

  function postCRM($data)
  {
    $sn = md5(CRM_POS_PWD);
    $postData = array(
                      'pos_no' => CRM_POS_NO,
                      'store_no' => CRM_STORE_NO,
                    );
    $postData = array_merge($data, $postData);
    ksort($postData);
    $str = '';
    foreach ($postData as $k=>$v) {
      $str .= "$k=$v&";
    }
    $str .= "sn=$sn";
    $sign = md5($str);

    $postData['sign'] = $sign;
    $postStr = http_build_query($postData);

    $url = CRM_POST_URL.'?'.$postStr;
    $a = httpPost($url);
    $b = simplexml_load_string($a);
    return $b;
  } 

  function getBizNo()
  {
    global $redis;

    $bizNo = date('ymd').rand(1000,9999);
    if ($redis->hexists('xibei_jifen_bizNo', $bizNo)) {
      getBizNo();
    } else {
      $redis->hset('xibei_jifen_bizNo', $bizNo, 1);
      return $bizNo;
    }
  }

	function getRandomStr()
	{

		$str = "";
		$str_pol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
		$max = strlen($str_pol) - 1;
		for ($i = 0; $i < 16; $i++) {
			$str .= $str_pol[mt_rand(0, $max)];
		}
		return $str;
	}
