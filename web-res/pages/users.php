<div class="container" >

    <div class="row" style="border-bottom: 1px gainsboro solid; padding:0px; margin:0px 0px 5px 0px;">
        <div class="col-sm-6" style="padding: 0px;"><h4 style="font-weight: bold; margin: 0px">Управление пользователями</h4></div>
        <div v-if="userName" class="col-sm-3" ><h4 style="margin:0px; color:#dd4b39; font-weight: bold;"><{{userName}}></h4></div>
        <div v-if="userName" class="col-sm-3" style="text-align: right; font-weight: bold; cursor:pointer; padding: 0px;">
             <button class="btn" @click="deleteDbUser()" style="font-weight: bold;border-radius: 0px">Удалить пользователя</button>
        </div>
    </div>

    <current-info :data_info="curUserInfo" ></current-info>

    <div class="row" style="border-bottom: 1px gainsboro solid; padding:0px; margin:5px 0px 0px 0px;" >

        <div class="col-sm-8" style="padding: 0px;">

            <button class="btn" data-toggle="modal" data-target="#set-db-privilegies" style="font-weight: bold;border-radius: 0px">Привязать базу</button>
            <button class="btn" data-toggle="modal" data-target="#del-db-privilegies" style="font-weight: bold;border-radius: 0px">Отвязать базу</button>

            <button class="btn" @click="setUserSuperStatus(userName)" style="font-weight: bold;border-radius: 0px">Set superuser</button>
            <button class="btn" @click="delUserSuperStatus(userName)()" style="font-weight: bold;border-radius: 0px">Del superuser</button>

        </div>

<!--        <div class="col-sm-3"></div>-->
<!--        <div class="col-sm-3" style="text-align: right; font-weight: bold; cursor:pointer; padding: 0px;"></div>-->
    </div>

<!--    <div class="table-responsive" style="width:30%" >-->
<!--        <table class="table">-->
<!--            <tr>-->
<!--                <td style="width:220px; border: 3px solid #ddd;" >Выбранный пользователь </td>-->
<!--                <td style="border: 3px solid #ddd;" >{{userName}}</td>-->
<!--            </tr>-->
<!--        </table>-->
<!--    </div>-->


    <div class="row">
        <div class="col-md-2" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a; "><h4 style="text-align:center" >Users list</h4></div>
        <div class="col-md-7" style="border-bottom: 0px solid #f7639a;"><h4 style="text-align:center">Пользователи</h4></div>
        <div class="col-md-3" style="border-bottom: 0px solid #f7639a;"><h4 style="text-align:center">Параметры</h4></div>
    </div>

    <!-- <pre>{{fileUsersConfig}}</pre> -->

    <div style="display: none">
        <div v-for="(db) in databaseList">
            <div>{{db.datname}}</div>
            <div style="display: none">
                <pre>{{db}}</pre>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- ЛЕВЫЙ БЛОК ----------->
        <div class="col-md-2" style="padding:0px 2px 2px 2px; border:0px gainsboro solid; min-height: 400px;">

            <div class="contact-us-detail"  style="cursor:pointer; text-align:center;position: relative; top:0px; left:0px; margin:2px 0px 4px 0px; border-radius: 0px; width: 100%">
                <a data-toggle="modal" style="display: block"
                   data-target="#add-new-user" href="#" >Добавить пользователя</a>
            </div>

            <ul class="portfolio" style="padding:0px; margin:0px;">
                <template v-for="(item, i) in usersList">
                    <li @click="setUserName(item)"
                        class="filter" style="display: block; text-align: left"><div>{{item.usename}}</div>
                    </li>
                </template>
            </ul>

        </div>

        <!-- СРЕДНИЙ БЛОК START --->
        <div @mousemove="dragMove($event)" class="col-md-7 dragCanvas" style="">

            <template v-for="(item, i) in usersList">
                <div @mousedown="dragInit($event)"
                     @mouseup="dragStop($event)"
                     @click="setUserName(item)"
                     :id="'move_el_' + item.usename" class="table-responsive dragElem"
                     style="left:20px; top:30px; width:240px;">
                    <table class="table" >
                        <tr v-for="(fvalue, fname) in item">
                            <td style="margin:0px; padding:5px;background-color: #4c9cef;color:white;"><div>{{fname}}</div></td>
                            <td style="margin:0px; padding:5px;background-color: #4c9cef;color:white;"><div>{{fvalue}}</div></td>
                        </tr>
                    </table>
                </div>
            </template>


            <div style="width:100px; margin:5px 0px 0px 86%;" >
                <div v-for="(us, uname) in fileUsersConfig" >
                    <div><pre>{{uname}}</pre></div>
                    <div style="display: none" ><pre>{{us}}</pre></div>
                </div>
            </div>

        </div>

        <!--  ПРАВЫЙ БЛОК  -------->
        <div class="col-md-3" style="padding:0px 2px 2px 2px; border:0px gainsboro solid">
<!--            <pre>{{fileUsersConfig}}</pre>-->
            <div class="table-responsive">
                <table class="table" >
                    <tr >
                        <td style="margin:0px; padding:5px;background-color: #4c9cef;color:white;"><div style="text-align:center;">Имя поля</div></td>
                        <td style="margin:0px; padding:5px;background-color: #4c9cef;color:white;"><div style="text-align:center;" >Значение</div></td>
                    </tr>

                    <tr v-for="(fvalue, fname) in userItem">
                        <td style="margin:0px; padding:5px;background-color: #4c9cef;color:white;"><div>{{fname}}</div></td>
                        <td style="margin:0px; padding:5px;background-color: #4c9cef;color:white;"><div>{{fvalue}}</div></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>

<modal-form
    modal_id="add-new-user"
    wrap_class="" >

    <template slot="title" >
        <h4>Добавить пользователя</h4>
    </template>

    <template slot="content" style="width:50%;" >

        <!--            <pre>{{newUser}}</pre>-->

        <div class="" style="" >
            <label style="font-style: italic;" >Имя пользователя</label>
            <input v-model="newUser.name" type="text" class="form-control" placeholder="Имя пользователя">
            <label style="font-style: italic;" >Пароль</label>
            <input v-model="newUser.password" type="text" class="form-control" placeholder="Пароль">

            <label style="font-style: italic;">Имя базы</label>
            <input v-model="newUser.dbName" type="text" class="form-control" placeholder="Имя базы для управления">

            <div style="display: flex; margin:10px; border-bottom: 1px gainsboro solid;" >

                <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="checkbox-1">
                    <input v-model="newUser.superUser" type="checkbox" id="checkbox-1" class="mdl-checkbox__input" >
                    <span class="mdl-checkbox__label">Супер пользователь</span>
                </label>

<!--                <label style="font-style: italic;">Супер пользователь</label>-->
<!--                <input v-model="newUser.superUser" type="checkbox" class="form-control1" style="margin-left:10%; cursor:pointer " >-->
            </div>

            <br><br>

            <div>
                <button @click="createDbUser()" class="btn" style="border-radius: 0px; width: 80%" data-dismiss="modal" >
                    Сохранить
                </button>
            </div>

        </div>

    </template>

</modal-form>


<modal-form modal_id="set-db-privilegies" >
    <template slot="title" ><h4>Привязать базу</h4></template>
    <template slot="content" style="width:50%;" >
        <div>
            <div>
                <label style="font-style: italic;" >Имя пользователя</label>
                <input v-model="userName" type="text" class="form-control" placeholder="Имя пользователя">
            </div>
            <div>
                <label style="font-style: italic;">Выбрать базу</label>
                <select v-model="selectDbName" class="form-control">
                    <option v-for="(item) in databaseList" :value="item.datname">{{item.datname}}</option>
                </select>
            </div>
            <br><br>
            <div v-if="userName" style="margin-top:10px;" >
                <button @click="setUserPrivileges(userName, selectDbName)"
                        class="btn" style="border-radius: 0px; width: 80%" data-dismiss="modal" >
                    Сохранить
                </button>
            </div>

        </div>
    </template>
</modal-form>

<modal-form modal_id="del-db-privilegies" >
    <template slot="title" ><h4>Отвязать базу</h4></template>
    <template slot="content" style="width:50%;" >
        <div>
            <div>
                <label style="font-style: italic;" >Имя пользователя</label>
                <input v-model="userName" type="text" class="form-control" placeholder="Имя пользователя">
            </div>
            <div>
                <label style="font-style: italic;">Выбрать базу</label>
                <select v-model="selectDbName" class="form-control">
                    <option v-for="(item) in databaseList" :value="item.datname">{{item.datname}}</option>
                </select>
            </div>
            <br><br>
            <div v-if="userName" style="margin-top:10px;" >
                <button @click="delUserPrivileges(userName, selectDbName)"
                        class="btn" style="border-radius: 0px; width: 80%" data-dismiss="modal" >
                    Сохранить
                </button>
            </div>

        </div>
    </template>
</modal-form>



<!--<div class="modal fade subscribe padding-top-120 in" id="subscribemodal" role="dialog"-->
<!--     style="display: block; padding-right: 17px;">-->
<!--    <div class="modal-dialog">-->
<!---->
<!--        <div class="modal-content">-->
<!--            <div class="modal-body">-->
<!--                <div class="row">-->
<!--                    <div class="col-sm-12">-->
<!--                        <div class="section-title margin-top-30">-->
<!--                            <button type="button" class="btn pull-right" data-dismiss="modal"><i-->
<!--                                        class="fa fa-close"></i></button>-->
<!--                            <h2>Subscribe.</h2>-->
<!--                            <div class="divider dark">-->
<!--                                <i class="icon-envelope-letter"></i>-->
<!--                            </div>-->
<!--                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit</p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!---->
<!--                <div class="row">-->
<!--                    <div class="col-sm-offset-2 col-xs-offset-0 col-md-8 col-sm-8">-->
<!---->
<!--                        <div class="margin-bottom-50">-->
<!--                            <form id="mc-form" >-->
<!--                                <div class="subscribe-form">-->
<!--                                    <input id="mc-email" type="email" placeholder="Email Address" class="text-input"-->
<!--                                           name="EMAIL">-->
<!--                                    <button class="submit-btn" type="submit">Submit</button>-->
<!--                                </div>-->
<!--                                <label for="mc-email" class="mc-label"></label>-->
<!--                            </form>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
