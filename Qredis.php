<?php
namespace v10086;
class Qredis {
    static $socket = null;

    //连接
    public static  function conn($host='192.168.139.128',$port=6379) {
        !self::$socket && self::$socket = fsockopen ($host, $port,$errno, $errmsg );
        if ($errno || $errmsg) 
            throw new \Exception($errno.'_'.$errmsg);     
    }
    
    //执行命令并返回响应
    public static  function exec($command) {
        self::reqWrite($command);
        return self::repReadAndParse();
    }
    
    //请求信息解释
    public static function reqParse($command){
        if (is_array($command)){	
            $s = '*'.count($command)."\r\n";
            foreach ($command as $m){
                $s.='$'.strlen($m)."\r\n";
                $s.=$m."\r\n";
            }
        }else{
            $s = $command . "\r\n";
        }
        return $s;
    }
    
    //请求信息写入
    public static function reqWrite($command){
        $s = self::reqParse($command);
        while ($s) {
            $i = fwrite (self::$socket,$s);
            if ($i == 0)
                break;
            $s = substr($s,$i);
        }
    }

    //响应信息读取
    public static function repRead(){
        $s = fgets(self::$socket);
        if(!$s){
            self::$socket && @fclose (self::$socket) && self::$socket=null;
            throw new \Exception ( "无法读取响应socket." );
        }
        return trim($s);
    }

    //响应信息读取并解析
    public static function repReadAndParse(){
        $s = self::repRead();
        switch ($s [0]) {
            case '-' :
                throw new \Exception(substr($s,1));
            case '+' : 
                return substr($s,1);
            case ':' :
                return substr($s,1)+0;
            case '$' :
                $i = (int)(substr ($s,1));
                if ($i == - 1){
                    return null;
                }
                $buffer = '';
                if ($i == 0){
                    $s = self::repRead();
                }
                while ( $i > 0 ) {
                    $s = self::repRead();
                    $l = strlen($s);
                    $i -= $l;
                    if ($i < 0){
                        $s = substr($s,0,$i);
                    }			
                    $buffer .= $s;
                }
                return $buffer;
            case '*' :
                $i = (int)(substr($s,1));
                if ($i == - 1){
                    return null; 
                }          
                $res = [];
                for($c = 0; $c < $i; $c ++) {
                    $res[] = self::repReadAndParse();
                }
                return $res;
            default :
                throw new \Exception ('无法解析响应信息:'.$s );
        }
        
    }
        

}