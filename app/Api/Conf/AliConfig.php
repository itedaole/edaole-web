<?php
return array(

/*

!!!!请将原先AlipayKeyChain文件夹删除

商户私钥为商家自行生成，支付宝公钥需要用商户私钥对应的公钥去支付宝商家后台换取

公钥注意事项，公钥必须以 -----BEGIN PUBLIC KEY----- 和 -----END PUBLIC KEY----- 包着，换行位置请勿自行修改
格式例如：
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCORBndEe18p0qgo4KKBfRAHtFzT8BVMddnzpT/
2aWldw2I1XUZKAbAjrmyBPsf/lUdC4sGhkoS2CvmQNjx2lu6/xsOQRW70sUIEvpZMo1RUUlh3Cxg
3f88cmN2Zwz2aCZWVXNSsGmQOQdeyKQviVtNxQ6S2hm4OAW4KIgxQzvhZQIDAQAB
-----END PUBLIC KEY-----

公钥注意事项，公钥必须以 -----BEGIN RSA PRIVATE KEY----- 和 -----END RSA PRIVATE KEY----- 包着，换行位置请勿自行修改
格式例如：
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQC6d2p9DdbBAGjoWWBiQQiG3rJSzarAeaAHgjwkBhAkdxMhftWC
d7qaWbgcpDy/7b42C9Mu5JBIy5LEO5apxWI+859HCtT8LTkSNCCNNOj26xW4iIo6
Iz99gwVYiOVRpApHhcbUADPQsZF0SwUVf37EqVRAdCUTeHYVy6nc1JLZcQIDAQAB
AoGABNngelpm0OSa1hilKkk42JFooEDbhWBkqm1X9TP3LYuvgrqTAW7t+uAEqzIW
poOf5DYQi3LE0jABpHtMYyRQdvltGZS/YvMIfNy0pdbzdDaQjWZXoMJwimzCBkvQ
LgQJQYJpR5vakBmwmHP0Arkj0Oah2T1hYzuINn+ZcNc05xUCQQDjKV5q3HU8Nw9E
h6kb60B3ZMUZTGw9gpXywTjjjnF9pG7INrPVkkvZrUr3MIvG/SN0V/XuiTD9IOh0
Ojzzx2zDAkEA0iN6I83uVFbcK0dt/hIbpeAaMCz4+raeZbUGmIFkjAGd7te+Gcnq
TfPBV/b4Wy8xbjqfBSOs5Dfk2VJuFRWNuwJAE6ncE3H77/dwKeV4XQNTNEKT0SnN
YNGx+y6ApyoIZvDZ6hjaHk2opTIcACPCpbn53LNUhY54oCC+HnmAFzYXEwJAVPkd
mb7bIeWh0CppMvUVkwTE8jgtUgxojs4d5atlAixhNcStzXXVtkHcK/rlQNIblexE
g2qCriJf+vUXKJV3owJAOFutnF8JCNSAzfwE2GGn6cOa/loQqsAIonL0G1+KwoSi
SxA5pYWeKlHT5NHKLggZkQtJiNy3i69PtuglbL7qSQ==
-----END RSA PRIVATE KEY-----


证书生成辅助工具
http://t.cn/8k7tIiw
*/
//以下为例子，请团长们自己生成自己的密钥

//支付宝公钥 
//去 https://ms.alipay.com/index.htm	--我的商家服务--账号信息（右上角）--密钥管理

	'PATH_ALI_PUB_PEM'=>'
-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCORBndEe18p0qgo4KKBfRAHtFz
T8BVMddnzpT/2aWldw2I1XUZKAbAjrmyBPsf/lUdC4sGhkoS2CvmQNjx2lu6/xsO
QRW70sUIEvpZMo1RUUlh3Cxg3f88cmN2Zwz2aCZWVXNSsGmQOQdeyKQviVtNxQ6S
2hm4OAW4KIgxQzvhZQIDAQAB
-----END PUBLIC KEY-----
',

//商户私钥

    'PATH_ALI_RSA_PRI_PEM' => '
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQC6d2p9DdbBAGjoWWBiQQiG3rJSzarAeaAHgjwkBhAkdxMhftWC
d7qaWbgcpDy/7b42C9Mu5JBIy5LEO5apxWI+859HCtT8LTkSNCCNNOj26xW4iIo6
Iz99gwVYiOVRpApHhcbUADPQsZF0SwUVf37EqVRAdCUTeHYVy6nc1JLZcQIDAQAB
AoGABNngelpm0OSa1hilKkk42JFooEDbhWBkqm1X9TP3LYuvgrqTAW7t+uAEqzIW
poOf5DYQi3LE0jABpHtMYyRQdvltGZS/YvMIfNy0pdbzdDaQjWZXoMJwimzCBkvQ
LgQJQYJpR5vakBmwmHP0Arkj0Oah2T1hYzuINn+ZcNc05xUCQQDjKV5q3HU8Nw9E
h6kb60B3ZMUZTGw9gpXywTjjjnF9pG7INrPVkkvZrUr3MIvG/SN0V/XuiTD9IOh0
Ojzzx2zDAkEA0iN6I83uVFbcK0dt/hIbpeAaMCz4+raeZbUGmIFkjAGd7te+Gcnq
TfPBV/b4Wy8xbjqfBSOs5Dfk2VJuFRWNuwJAE6ncE3H77/dwKeV4XQNTNEKT0SnN
YNGx+y6ApyoIZvDZ6hjaHk2opTIcACPCpbn53LNUhY54oCC+HnmAFzYXEwJAVPkd
mb7bIeWh0CppMvUVkwTE8jgtUgxojs4d5atlAixhNcStzXXVtkHcK/rlQNIblexE
g2qCriJf+vUXKJV3owJAOFutnF8JCNSAzfwE2GGn6cOa/loQqsAIonL0G1+KwoSi
SxA5pYWeKlHT5NHKLggZkQtJiNy3i69PtuglbL7qSQ==
-----END RSA PRIVATE KEY-----
',

// //商户公钥
//     'PATH_ALI_RSA_PUB_PEM' => '
// -----BEGIN RSA PRIVATE KEY-----
// MIICWwIBAAKBgQC6d2p9DdbBAGjoWWBiQQiG3rJSzarAeaAHgjwkBhAkdxMhftWC
// d7qaWbgcpDy/7b42C9Mu5JBIy5LEO5apxWI+859HCtT8LTkSNCCNNOj26xW4iIo6
// Iz99gwVYiOVRpApHhcbUADPQsZF0SwUVf37EqVRAdCUTeHYVy6nc1JLZcQIDAQAB
// AoGABNngelpm0OSa1hilKkk42JFooEDbhWBkqm1X9TP3LYuvgrqTAW7t+uAEqzIW
// poOf5DYQi3LE0jABpHtMYyRQdvltGZS/YvMIfNy0pdbzdDaQjWZXoMJwimzCBkvQ
// LgQJQYJpR5vakBmwmHP0Arkj0Oah2T1hYzuINn+ZcNc05xUCQQDjKV5q3HU8Nw9E
// h6kb60B3ZMUZTGw9gpXywTjjjnF9pG7INrPVkkvZrUr3MIvG/SN0V/XuiTD9IOh0
// Ojzzx2zDAkEA0iN6I83uVFbcK0dt/hIbpeAaMCz4+raeZbUGmIFkjAGd7te+Gcnq
// TfPBV/b4Wy8xbjqfBSOs5Dfk2VJuFRWNuwJAE6ncE3H77/dwKeV4XQNTNEKT0SnN
// YNGx+y6ApyoIZvDZ6hjaHk2opTIcACPCpbn53LNUhY54oCC+HnmAFzYXEwJAVPkd
// mb7bIeWh0CppMvUVkwTE8jgtUgxojs4d5atlAixhNcStzXXVtkHcK/rlQNIblexE
// g2qCriJf+vUXKJV3owJAOFutnF8JCNSAzfwE2GGn6cOa/loQqsAIonL0G1+KwoSi
// SxA5pYWeKlHT5NHKLggZkQtJiNy3i69PtuglbL7qSQ==
// -----END RSA PRIVATE KEY-----
// ',


    );

?>