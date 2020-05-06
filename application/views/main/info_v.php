<div id="cs">



    <div class="row">
        <div class="col-lg-12 mt-3">
            <div class="card shadow-lg mb-5">
                <div class="card-header">
                    <h3 class="text-center text-uppercase">{{ info.HostName }}</h3>
                </div>
                <div class="card-body">
                    <div class="card-title">
                        <strong>Описание:</strong> {{ info.ModDesc }}<br>
                        <strong>Тэги поиска:</strong> {{ info.GameTags }}
                    </div>
                    <p>
                        <strong>Античит:</strong>
                        <span v-if="info.Secure == 1" class="text-success">Вкл</span>
                        <span v-if="info.Secure == 0" class="text-danger">Выкл</span>
                    </p>
                    <p>
                        <strong>Карта:</strong> {{ info.Map }}
                    </p>
                    <p>
                        <strong>Ботов:</strong> {{ info.Bots }}
                    </p>
                    <h5 class="text-center">Выполнить Rcon команду</h5>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Введите rcon команду" aria-label="Введите rcon команду" aria-describedby="button-addon2" v-model="cmd" @keyup.enter="rcon()">
                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="button" id="button-addon2" @click="rcon()">Выполнить</button>
                        </div>
                    </div>
                    <div class="alert alert-success" v-if="msgS">
                        <p v-html="br(msgS)"></p>
                        <button @click="msgS = false; cmd = '';" class="btn btn-sm btn-danger">отчистить</button>
                    </div>
                    <div class="alert alert-danger" v-if="msgE">
                        <p v-html="br(msgE)"></p>
                        <button @click="msgE = false; cmd = '';" class="btn btn-sm btn-danger">отчистить</button>
                    </div>
                    <div class="" v-if="players.length > 0">
                        <h5 class="text-center">Игроки: {{ info.Players }}/{{ info.MaxPlayers }}
                            <button class="btn btn-sm btn-primary" @click="getPlayers">Обновить</button>
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Ник</th>
                                    <th>Steam ID</th>
                                    <th>Ранг</th>
                                    <th>Фрагов</th>
                                    <th>Время</th>
                                    <th>Всего килов</th>
                                    <th>Всего смертей</th>
                                    <th>Headshots</th>
                                    <th>Выигранных матчей</th>
                                    <th>Проигранных матчей</th>
                                    <th>Управление</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(u, index) in players">
                                    <td>
                                        {{ index+1 }}
                                    </td>
                                    <td>
                                        {{ u.Name }}
                                    </td>

                                    <td>
                                        {{ u.SteamID }}
                                    </td>
                                    <td>
                                        {{ u.Rank }}
                                    </td>
                                    <td>
                                        {{ u.Frags }}
                                    </td>
                                    <td>
                                        {{ u.TimeF }}
                                    </td>
                                    <td>
                                        {{ u.Kills }}
                                    </td>
                                    <td>
                                        {{ u.Deaths }}
                                    </td>
                                    <td>
                                        {{ u.Headshots }}
                                    </td>
                                    <td>
                                        {{ u.RoundWin }}
                                    </td>
                                    <td>
                                        {{ u.RoundLose }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info" @click="kick(u.Name)">Кик</button>
                                        <button class="btn btn-sm btn-outline-danger" @click="ban(u.SteamID)">Бан</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>


    const cs = new Vue({
        el: '#cs',
        data: {
            info: <?=$info?>,
            players: '',
            cmd: '',
            msgS: false,
            msgE: false,
        },
        mounted() {
            this.getPlayers('<?=$this->uri->segment(3)?>');
        },
        methods: {
            getPlayers(){
                axios.get('<?=base_url("main/get_players/").$this->uri->segment(3)?>')
                    .then(
                        (response) => {
                            this.players = response.data;
                        })
                    .catch((error) => {
                        console.log(error.response.data);
                    });
            },
            kick(name){
                var conf = confirm("Вы уверены что хотите кикнуть "+name+"?");
                if (conf){
                    const body = new FormData();
                    body.set('name', name);

                    axios({
                        method: 'post',
                        url: '<?=base_url("main/kick/").$this->uri->segment(3)?>',
                        data: body,
                    }).then((response) => {
                        //this.msgS = response.data;
                        //this.msgE = false;
                    }).catch((error) => {
                        //this.msgE = error.response.data;
                        //this.msgS = false;
                        console.log(error.error);
                    });
                }
            },
            ban(name, steam){
                var conf = confirm("Вы уверены что хотите забанить "+name+"?");
                if (conf){
                    const body = new FormData();
                    body.set('steam', steam);

                    axios({
                        method: 'post',
                        url: '<?=base_url("main/ban/").$this->uri->segment(3)?>',
                        data: body,
                    }).then((response) => {
                        //this.msgS = response.data;
                        //this.msgE = false;
                    }).catch((error) => {
                        //this.msgE = error.response.data;
                        //this.msgS = false;
                        console.log(error.error);
                    });
                }
            },
            rcon(){
                const body = new FormData();
                body.set('cmd', this.cmd);

                axios({
                    method: 'post',
                    url: '<?=base_url("main/exec_rcon/").$this->uri->segment(3)?>',
                    data: body,
                }).then((response) => {
                    this.msgS = response.data;
                    this.msgE = false;
                }).catch((error) => {
                    this.msgE = error.response.data;
                    this.msgS = false;
                    console.log(error.error);
                });
            },
            getDateNow(){
                var now = new Date();
                return now.getTime();
            },
            br: function (str) {
                return  str.replace(/\n/g, "<br>").replace(/           /g, "&nbsp;&nbsp;&nbsp;&nbsp;");
            },
        },
        filters: {
            date: function (dateNews) {
                return new Date(+dateNews * 1000).toLocaleString('ru-RU');
            }
        },

    });
</script>