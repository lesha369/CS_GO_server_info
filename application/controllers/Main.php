<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        if ($this->uri->segment(3)){
            $server = $this->security->xss_clean($this->uri->segment(3));
            $this->load->database($server);
        }
    }

    public function index()
	{
	    $serverList = file_get_contents(APPPATH.'server_list.json');
	    $data['server_list'] = $serverList;
	    $serverList = json_decode($serverList);

	    if ($this->uri->segment(3)){
	        $server = $this->security->xss_clean($this->uri->segment(3));
            $info = $this->cs_m->info_server($serverList->$server->addr, $serverList->$server->port, 'info');

            //print_r($players);

            $data['info'] = json_encode($info, JSON_UNESCAPED_UNICODE);
            //$data['players'] = json_encode($players, JSON_UNESCAPED_UNICODE);

            $this->load->view('header');
            $this->load->view('main/info_v', $data);
            $this->load->view('footer');
        }
	    else{
            $this->load->view('header');
            $this->load->view('main/index_v', $data);
            $this->load->view('footer');
        }
	}

	function get_all_users(){
        $res = $this->db->get('lvl_base')->result_array();
        usort($players, function($a, $b){
            return ($b['value'] - $a['value']);
        });
        print_r(json_encode($res));
    }

	function info_server(){
	    $addr = $this->security->xss_clean($this->input->post('addr'));
	    $port = $this->security->xss_clean($this->input->post('port'));
        $info = $this->cs_m->info_server($addr, $port, 'info');
        print_r(json_encode($info, JSON_UNESCAPED_UNICODE));
    }

    function get_players(){
        $server = $this->security->xss_clean($this->uri->segment(3));
        $serverList = file_get_contents(APPPATH.'server_list.json');
        $serverList = json_decode($serverList);

        $playersLib = $this->cs_m->info_server($serverList->$server->addr, $serverList->$server->port, 'players');
        $activeUsers = array_map(function ($e){
            return $e['Name'];
        }, $playersLib);

        $this->db->where_in('name', $activeUsers);
        $res = $this->db->get('lvl_base')->result();

        $players = [];
        foreach ($res as $row){
            //print_r(htmlspecialchars($row->name));
            $found = array_search(htmlspecialchars($row->name), array_column($playersLib, 'Name'));
            if ($found !== false){
                $players[] = [
                    'SteamID' => $row->steam,
                    'Name' => htmlspecialchars($playersLib[$found]['Name']),
                    'Frags' => $playersLib[$found]['Frags'],
                    'Time' => $playersLib[$found]['Time'],
                    'TimeF' => $playersLib[$found]['TimeF'],
                    'Rank' => $row->value,
                    'Kills' => $row->kills,
                    'Deaths' => $row->deaths,
                    'Shoots' => $row->shoots,
                    'Hits' => $row->hits,
                    'Headshots' => $row->headshots,
                    'RoundWin' => $row->round_win,
                    'RoundLose' => $row->round_lose,
                ];
            }
        }
        usort($players, function($a, $b){
            return ($b['Rank'] - $a['Rank']);
        });
        print_r(json_encode($players, JSON_UNESCAPED_UNICODE));
    }

    function get_global_stat(){
        $res = $this->db->order_by('value', 'DESC')->get('lvl_base')->result_array();
        print_r(json_encode($res, JSON_UNESCAPED_UNICODE));
    }

    function exec_rcon(){
        $server = $this->security->xss_clean($this->uri->segment(3));
        $serverList = file_get_contents(APPPATH.'server_list.json');
        $serverList = json_decode($serverList);

        if ($this->input->post('cmd')){
            $cmd = $this->security->xss_clean($this->input->post('cmd'));
            $result = $this->cs_m->exec_rcon($serverList->$server->addr, $serverList->$server->port, $cmd, $serverList->$server->rcon_pass);
            print_r($result);
        }
        else{
            $this->output->set_status_header(400);
            echo 'Введите Rcon команду';
        }
    }

    function kick(){
        $server = $this->security->xss_clean($this->uri->segment(3));
        $serverList = file_get_contents(APPPATH.'server_list.json');
        $serverList = json_decode($serverList);

        if ($this->input->post('name')){
            $name = $this->security->xss_clean($this->input->post('name'));
            $result = $this->cs_m->exec_rcon($serverList->$server->addr, $serverList->$server->port, 'kick "'.$name.'"', $serverList->$server->rcon_pass);
            print_r($result);
        }
        else{
            $this->output->set_status_header(400);
            echo 'Введите name игрока';
        }
    }

    function ban(){
        $server = $this->security->xss_clean($this->uri->segment(3));
        $serverList = file_get_contents(APPPATH.'server_list.json');
        $serverList = json_decode($serverList);

        if ($this->input->post('steam')){
            $name = $this->security->xss_clean($this->input->post('steam'));
            $result = $this->cs_m->exec_rcon($serverList->$server->addr, $serverList->$server->port, 'banid 0 "'.$name.'"', $serverList->$server->rcon_pass);
            print_r($result);
        }
        else{
            $this->output->set_status_header(400);
            echo 'Введите name игрока';
        }
    }

}
