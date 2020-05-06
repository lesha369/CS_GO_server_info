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
                    <h5 class="text-center">Отправить всем сообщение</h5>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" placeholder="Введите сообщение" aria-label="Введите сообщение" aria-describedby="button-addon2" v-model="mess" @keyup.enter="sendMes()">
                        <div class="input-group-append">
                            <button class="btn btn-outline-success" type="button" id="button-addon2" @click="sendMes()">Отправить</button>
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
                        <h5 class="text-center">Игроки: {{ players.length }}/{{ info.MaxPlayers }}
                            <button class="btn btn-sm btn-primary" @click="getPlayers">Обновить</button>
                        </h5>
                        <button class="btn btn-sm btn-success mb-3" @click="showGlobalStat()" data-toggle="modal" data-target="#myModal">Показать общую статистику по рангу</button>
                        <!--MODAL-->
                        <modal>
                            <h3 class="text-center">Всего игроков: {{ globalStat.length }}</h3>
                            <div class="table-responsive" v-if="globalStat">
                                <table class="table table-hover">
                                    <thead class="table-primary">
                                    <tr>
                                        <th>#</th>
                                        <th>SteamID</th>
                                        <th>Ник</th>
                                        <th>Ранг</th>
                                        <th>Килов</th>
                                        <th>Смертей</th>
                                        <th>Headshots</th>
                                        <th>Выигранных матчей</th>
                                        <th>Проигранных матчей</th>
                                        <th>Проведено времени на сервере</th>
                                        <th>Последнее подключение</th>
                                        <th>Управление</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(s, num) in globalStat">
                                        <td>{{ num+1 }}</td>
                                        <td>{{ s.steam }}</td>
                                        <td>{{ s.name }}</td>
                                        <td>{{ s.value }}</td>
                                        <td>{{ s.kills }}</td>
                                        <td>{{ s.deaths }}</td>
                                        <td>{{ s.headshots }}</td>
                                        <td>{{ s.round_win }}</td>
                                        <td>{{ s.round_lose }}</td>
                                        <td>{{ convertSecInH(s.playtime) }}</td>
                                        <td>{{ s.lastconnect | date }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-info" @click="kick(s.name)">Кик</button>
                                            <button class="btn btn-sm btn-outline-danger" @click="ban(s.steam)">Бан</button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </modal>
                        <!--/MODAL-->
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

    Vue.component('modal', {
        data: () => {
            return {}
        },
        props: [],
        methods: {

        },
        template: `<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel"><strong>Статистика: </strong></h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                </div>
                                <div class="modal-body">
                                    <slot></slot>
                                </div>
                            </div>
                        </div>
                    </div>`
    });



    const cs = new Vue({
        el: '#cs',
        data: {
            info: <?=$info?>,
            players: '',
            cmd: '',
            mess: '',
            globalStat: false,
            msgS: false,
            msgE: false,
        },
        mounted() {
            this.getPlayers('<?=$this->uri->segment(3)?>');
        },
        methods: {
            showGlobalStat(){
                axios.get('<?=base_url("main/get_global_stat/").$this->uri->segment(3)?>')
                    .then(
                        (response) => {
                            this.globalStat = response.data;
                        })
                    .catch((error) => {
                        this.globalStat = false;
                        console.log(error.response.data);
                    });
            },
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
            sendMes(){
                const body = new FormData();
                body.set('cmd', 'sm_msay "'+this.mess+'"');

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
            convertSecInH: function(timestamp){
                var hours = Math.floor(timestamp / 60 / 60);
                var minutes = Math.floor(timestamp / 60) - (hours * 60);
                var seconds = timestamp % 60;

                var str = '';
                str += (hours === 0) ? '' : hours+'ч. ';
                str += (minutes === 0) ? '' : minutes+'мин. ';
                str += (seconds === 0) ? '' : seconds+'сек. ';

                return str
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