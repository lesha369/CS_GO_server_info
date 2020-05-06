<div id="cs">



    <div class="container">
        <h1 class="text-center text-primary mt-4">Выберите сервер:</h1>
        <div class="card shadow-lg mt-4" v-for="(s, name) in serverList" >
            <div class="card-body" v-if="serverData[name]">
                <div class="card-title">
                    <h3 class="text-center text-info">{{ serverData[name].HostName }}</h3>
                </div>
                <p class="text-center">
                    <a :href="'<?=base_url('main/index/')?>'+name" class="btn btn-success">Управление</a>
                </p>
            </div>
        </div>
    </div>

</div>

<script>


    const cs = new Vue({
        el: '#cs',
        data: {
            serverList: <?=$server_list?>,
            serverData: {},
        },
        mounted() {
            this.getInfoAllServers();
        },
        methods: {
            getInfoAllServers(){
                for (var i in this.serverList){
                    this.infoServer(i, this.serverList[i].addr, this.serverList[i].port);
                    //console.log(i);
                }
            },
            infoServer(serverName, addr, port){
                const body = new FormData();
                body.set('addr', addr);
                body.set('port', port);

                axios({
                    method: 'post',
                    url: '<?=base_url("main/info_server")?>',
                    data: body,
                }).then((response) => {
                    this.$set(this.serverData, serverName, response.data)
                }).catch((error) => {
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