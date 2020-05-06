<?php
use xPaw\SourceQuery\SourceQuery;
require '/home/hack-dag/sites/cs/SourceQuery/bootstrap.php';

class Cs_m extends CI_Model
{
    function server_list(){
        $list = file_get_contents(FCPATH.'server_list.json');
        $data['list'] = $list;

    }

    function info_server($addr, $port = 27015, $request = 'info'){
        $Query = new SourceQuery( );

        if ($request == 'info'){
            $return = false;
            try
            {
                $Query->Connect( $addr, $port, 1, SourceQuery::SOURCE );
                $return = $Query->GetInfo();
            }
            catch( Exception $e )
            {
                echo $e->getMessage( );
            }
            finally
            {
                $Query->Disconnect( );
            }
            return $return;
        }
        if ($request = 'players'){
            $return = false;
            try
            {
                $Query->Connect( $addr, $port, 1, SourceQuery::SOURCE );
                $return = $Query->GetPlayers();
            }
            catch( Exception $e )
            {
                echo $e->getMessage( );
            }
            finally
            {
                $Query->Disconnect( );
            }
            return $return;
        }
    }

    function exec_rcon($addr, $port = 27015, $cmd, $pass){
        $Query = new SourceQuery( );
        $Query->Connect( $addr, $port, 1, SourceQuery::SOURCE );
        $Query->SetRconPassword($pass);
        $result = $Query->Rcon($cmd);
        return $result;
    }

    function css_online($ip, $port, $request)
    {
        $fp = @fsockopen("udp://$ip", $port, $errno, $errstr, 1);
        if (!$fp) {
            return FALSE;
        }
        stream_set_timeout($fp, 1, 0);
        stream_set_blocking($fp, true);
        if ($request == "settings" || $request == "players") {
            $challenge_code = "\xFF\xFF\xFF\xFF\x57";
            fwrite($fp, $challenge_code);
            $buffer = fread($fp, 4096);
            if (!trim($buffer)) {
                fclose($fp);
                return FALSE;
            }
            $challenge_code = substr($buffer, 5, 4);
        }
        if ($request == "info") {
            $challenge = "\xFF\xFF\xFF\xFFTSource Engine Query\x00";
        }
        if ($request == "players") {
            $challenge = "\xFF\xFF\xFF\xFFU" . $challenge_code;
        }
        if ($request == "settings") {
            $challenge = "\xFF\xFF\xFF\xFFV" . $challenge_code;
        }
        fwrite($fp, $challenge);
        $buffer = fread($fp, 4096);
        if (!$buffer) {
            fclose($fp);
            return FALSE;
        }
        if ($request == "settings") {
            $second_packet = fread($fp, 4096);
            if (strlen($second_packet) > 0) {
                $reverse_check = dechex(ord($buffer[8]));
                if ($reverse_check[0] == "1") {
                    $tmp = $buffer;
                    $buffer = $second_packet;
                    $second_packet = $tmp;
                }
                $buffer = substr($buffer, 13);
                $second_packet = substr($second_packet, 9);
                $buffer = trim($buffer . $second_packet);
            } else {
                $buffer = trim(substr($buffer, 4));
            }
        } else {
            $buffer = trim(substr($buffer, 4));
        }
        fclose($fp);
        if (!trim($buffer)) {
            return FALSE;
        }
        if ($request == "info") {
            $tmp = substr($buffer, 2);
            $tmp = explode("\x00", $tmp);
            $place = strlen($tmp[0] . $tmp[1] . $tmp[2] . $tmp[3]) + 8;
            $data['gamemod'] = $tmp[2];
            $data['hostname'] = $tmp[0];
            $data['mapname'] = $tmp[1];
            $data['players'] = ord($buffer[$place]);
            $data['maxplayers'] = ord($buffer[$place + 1]);
            $data['password'] = ord($buffer[$place + 5]);
            $data['datatype'] = $buffer[0];
            $data['version'] = ord($buffer[1]);
            $data['description'] = $tmp[3];
            $data['botplayers'] = ord($buffer[$place + 2]);
            $data['server_type'] = $buffer[$place + 3];
            $data['server_os'] = $buffer[$place + 4];
            $data['server_bots'] = ord($buffer[$place + 2]);
            $data['server_secure'] = ord($buffer[$place + 6]);
            if ($data['datatype'] != "I") {
                return FALSE;
            }
            return $data;
        }
        if ($request == "players") {
            $player_number = 0;
            $position = 2;
            do {
                $player_number++;
                $player[$player_number]['id'] = ord($buffer[$position]);
                $position++;
                while ($buffer[$position] != "\x00" && $position < 4000) {
                    $player[$player_number]['name'] .= $buffer[$position];
                    $position++;
                }
                $player[$player_number]['score'] = (ord($buffer[$position + 1]))
                    + (ord($buffer[$position + 2]) * 256)
                    + (ord($buffer[$position + 3]) * 65536)
                    + (ord($buffer[$position + 4]) * 16777216);
                if ($player[$player_number]['score'] > 2147483648) {
                    $player[$player_number]['score'] -= 4294967296;
                }
                $time = substr($buffer, $position + 5, 4);
                if (strlen($time) < 4) {
                    return FALSE;
                }
                list(, $time) = unpack("f", $time);
                $time = mktime(0, 0, $time);
                $player[$player_number]['time'] = date("H:i:s", $time);
                $position += 9;
            } while ($position < strlen($buffer));
            return $player;
        }
        if ($request == "settings") {
            $tmp = substr($buffer, 2);
            $rawdata = explode("\x00", $tmp);
            for ($i = 1; $i < count($rawdata); $i = $i + 2) {
                $rawdata[$i] = strtolower($rawdata[$i]);
                $setting[$rawdata[$i]] = $rawdata[$i + 1];
            }
            return $setting;
        }
    }

}