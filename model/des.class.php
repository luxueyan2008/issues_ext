<?php
header('Content-type: text/html;charset=utf-8'); 
class Des
{
    public static function pkcs5_pad ($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    public static function pkcs5_unpad($text) {
        $pad = ord($text{strlen($text)-1});
        if ($pad > strlen($text)) {
            return false;
        }
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        return substr($text, 0, -1 * $pad);
    }

    public static function encrypt($key, $data) {
        $size = mcrypt_get_block_size('des', 'ecb');
        $data = Des::pkcs5_pad($data, $size);
        $td = mcrypt_module_open('des', '', 'ecb', '');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        @mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $data;
    }

    public static function decrypt($key, $data) {
        $td = mcrypt_module_open('des','','ecb','');
        $iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        $ks = mcrypt_enc_get_key_size($td);
        @mcrypt_generic_init($td, $key, $iv);
        $decrypted = mdecrypt_generic($td, $data);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $result = Des::pkcs5_unpad($decrypted);
        return $result;
    }
}

/*  test code

$in = "04fMaWegkH1/BL9CNYxgusFpYK8wdraBX06mPiRmxJP+uVm31GQvyw==";
$des = base64_decode($in);
echo DES::decrypt("12345678", $des);
echo "<br/>";

$in = "cea3e8e1659582206e0be32539729e9f";
$des = DES::encrypt("12345678", $in);
$out = base64_encode($des);
echo $out;
echo "<br/>";

// */

?>
