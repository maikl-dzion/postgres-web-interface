<div class="container">

    <div class="row" style="border-bottom: 1px gainsboro solid; padding:0px;margin:0px;">
        <div class="col-sm-6" style="padding: 0px;"><h4 style="font-weight: bold; margin:0px; font-style:italic;">Настройки</h4></div>
    </div>

    <current-info :data_info="curUserInfo" style="margin:4px 0px 4px 0px;" ></current-info>

    <div class="row" style="border-bottom: 1px gainsboro solid; padding:0px;margin:5px 0px 0px 0px;">
        <div class="col-sm-8" style="padding: 0px;">
            <button class="btn" data-toggle="modal" data-target="#get-db-dump" style="font-weight: bold;border-radius: 0px" >Получить дамп базы</button>
            <button @click="setDefaultConfig()" class="btn"  style="font-weight: bold;border-radius: 0px" >Настройки по умолчанию</button>
        </div>

        <div class="col-sm-4" style="padding: 0px; display: flex">
            <a v-if="remoteUrl" :href="remoteUrl" style="color:red; width:50%; margin-left:auto; text-align:right; display: block" >Скачать sql-dump</a>
        </div>

    </div>


    <div class="row" >
        <div class="col-md-3" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a; "><h4>Текущий конф.</h4></div>
        <div class="col-md-6" style="border-bottom: 0px solid #f7639a;"><h4 style="text-align:center"></h4></div>
        <div class="col-md-3" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a;"><h4 style="text-align:center">Новый конф.</h4></div>
    </div>

    <div class="row">

        <!-- ЛЕВЫЙ БЛОК -->
        <div class="col-md-3" style="padding:0px 2px 2px 2px; border:0px gainsboro solid; min-height: 400px;">
            <div v-for="(val, name) in dbConfPrint" class="table-responsive" style="" >
                <table class="table">
                    <tr><td style="width:40%; margin:0px; padding:4px;" ><div style="width:100%;">{{name}}</div></td>
                        <td style="width:60%; margin:0px; padding:4px;" ><div style="width:100%;">{{val}}</div></td>
                    </tr>
                </table>
            </div>
        </div>


        <!-- СРЕДНИЙ БЛОК START --->
        <div @mousemove="dragMove($event)" class="col-md-6 dragCanvas" style="">

        </div>

        <!--  ПРАВЫЙ БЛОК  -------->
        <div class="col-md-3" style="padding:0px 2px 2px 2px; border:0px gainsboro solid">

<!--             <pre>{{dbConf}}</pre>-->
            <div class="contact-us-detail" style="cursor:pointer; text-align:center;position: relative; top:0px; left:0px; margin:2px 0px 4px 0px; border-radius: 0px; width: 100%">
                <a  style="text-align:center; display: block">Создать конф.</a></div>
            <div v-for="(val, name) in dbConf" style="display: flex; transition: width 2s, height 2s, background-color 2s, transform 2s; ">
                <div style="width: 25%; border:0px grey solid; text-align:left; padding-left:5px;">{{name}}</div>

                <div v-if="name == 'user'" style="width:75%;">
                    <select v-model="dbConf[name]" @change="selectUserPassword(dbConf[name])" class="form-control">
                        <option v-for="(item) in usersList" :value="item.usename">{{item.usename}}</option>
                    </select>
                </div>
                <div v-else-if="name == 'dbname'" style="width:75%;">
                    <select v-model="dbConf[name]" class="form-control">
                        <option v-for="(item) in databaseList" :value="item.datname">{{item.datname}}</option>
                    </select>
                </div>
                <div v-else style="width:75%;">
                    <input v-model="dbConf[name]" type="text" class="form-control" >
                </div>

            </div>
            <button @click="saveConfig()" class="btn btn-primary" style="width:100%; border-radius: 0px; margin:10px 5px 5px 0px;">Сохранить</button>

        </div>

    </div>
</div>


<modal-form modal_id="get-db-dump" >
    <template slot="title" ><h4>Привязать базу</h4></template>
    <template slot="content" style="width:50%;" >
        <div>
            <div>

                <label style="font-style: italic;">Выбрать пользователя</label>
                <select v-model="userName" class="form-control">
                    <option v-for="(item) in usersList" :value="item.usename">{{item.usename}}</option>
                </select>
            </div>

            <div>
                <label style="font-style: italic;" >Пароль</label>
                <input v-model="userPassword" type="text" class="form-control" placeholder="Пароль">
            </div>

            <div>
                <label style="font-style: italic;">Выбрать базу</label>
                <select v-model="selectDbName" class="form-control">
                    <option v-for="(item) in databaseList" :value="item.datname">{{item.datname}}</option>
                </select>
            </div>
            <br><br>
            <div v-if="userName" style="margin-top:10px;" >
                <button @click="getDbDump(userName, userPassword, selectDbName)"
                        class="btn" style="border-radius: 0px; width: 80%" data-dismiss="modal" >
                        Выполнить
                </button>
            </div>

        </div>
    </template>
</modal-form>