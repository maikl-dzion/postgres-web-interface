<div class="container" style="border:0px gainsboro solid" >

    <div class="row" >
        <div class="col-sm-6" style="padding: 0px;"><h4 style="font-weight: bold; margin:0px; font-style:italic;">Common page</h4></div>
    </div> <hr>

    <current-info
        :data_info="curUserInfo"
     ></current-info> <hr>

    <div class="row" >
       <sql-query-form
          @get_query_response="sqlQueryResult"
       ></sql-query-form>
    </div> <hr>

    <div class="row" >
        <div class="col-sm-5 commonButtonsPanel" >
            <button @click="commonAction('users')"          class="btn customBtn" >Пользователи</button>
            <button @click="commonAction('tables')"         class="btn customBtn" >Таблицы</button>
            <button @click="commonAction('databases')"      class="btn customBtn" >Базы</button>
            <button @click="commonAction('form-edit-com')"  class="btn customBtn" >Форма</button>
            <button @click="sqlCommandRun('get_roles')"     class="btn customBtn" >Роли</button>
        </div>

        <div class="col-sm-7 commonButtonsPanel" style="text-align:right" >

            <button class="btn customBtn"
                    data-toggle="modal"
                    data-target="#add-new-db-modal" >Add db</button>

            <button class="btn customBtn"
                    data-toggle="modal"
                    data-target="#add-new-user-modal" >Add user</button>

            <button class="btn customBtn"
                    data-toggle="modal"
                    data-target="#add-new-table-modal" >Add table</button>

            <button v-if="tableName"
                    class="btn customBtn"
                    data-toggle="modal"
                    data-target="#add-new-field-modal" >Add field</button>
        </div>
    </div><hr>

    <div class="row" >
        <div class="col-sm-12 commonButtonsPanel" style="text-align:left" >

            <button class="btn customBtn"
                    data-toggle="modal"
                    data-target="#set-db-privilegies" >Привязать пользователя к базе</button>

            <button class="btn customBtn"
                    data-toggle="modal"
                    data-target="#delele-db-privilegies" >Отвязать пользователя от базы</button>

            <button v-if="userName" class="btn customBtn"
                    @click="setUserSuperStatus(userName)" >Set superuser</button>
            <button v-if="userName" class="btn customBtn"
                    @click="delUserSuperStatus(userName)()" >Del superuser</button>

        </div>
    </div><hr>

    <!-- ########################## ---->
    <!-------  MAIN CONTET / Start ----->
    <div class="row" >
        <div class="col-md-5" style="text-align:center; border-bottom: 4px solid #f7639a;" >
            <h4 style="text-align:center" >{{commonTitle}}</h4>
        </div>
        <div class="col-md-7" style="border-bottom: 4px solid #f7639a;">
            <h4 style="text-align:center" >Панель редактирования</h4>
        </div>
    </div>


    <div class="row" >

        <select-object-info
           :info="{ table: tableName, user : userName, db : selectDbName }"
        ></select-object-info>

        <!-- ЛЕВЫЙ БЛОК    --->
        <div class="col-md-5 commonLeftPanel" >
              <ul class="portfolio" style="border:0px red solid; margin:0px; padding:0px;" >
                  <template v-if="commonActionName == 'users'" >

                      <li  class="filter" style="display: block; text-align: left; background: none; margin:0px; padding:0px" >
                          <table class="table commonLeftTable" >
                              <tr v-for="(item, i) in usersList" >
                                  <td @click="commonForm(item)"
                                      class="commonLeftTableTd" style="width:91%; color:green; font-style: italic; " >
                                      <div >{{item.usename}}</div>
                                  </td>

                                  <td class="commonLeftTableTd" style="background: none; text-align:center" >
                                      <template v-if="item.usename != 'postgres' &&
                                                      item.usename != 'w1user'" >
                                          <img @click="deleteDbUser(item.usename)"
                                               src="images/delete-icon.png" style="width: 20px; cursor:pointer;" title="Удалить" >
                                      </template>
                                  </td>
                              </tr>
                          </table>
                      </li>

                  </template>
                  <template v-else-if="commonActionName == 'databases'" >

                        <li  class="filter" style="display: block; text-align: left; background: none; margin:0px; padding:0px" >
                          <table class="table commonLeftTable" >
                              <tr v-for="(item, i) in databaseList" >

                                  <td @click="commonForm(item)"
                                      class="commonLeftTableTd" style="width:91%; color:green; font-style: italic; " >
                                      <div >{{item.datname}}</div>
                                  </td>

                                  <td class="commonLeftTableTd" style="background: none; text-align:center" >
                                      <template v-if="item.datname != 'template1' &&
                                                      item.datname != 'template0' &&
                                                      item.datname != 'maikldb'   &&
                                                      item.datname != 'postgres'" >
                                                <img @click="deleteDb(item.datname)"
                                                     src="images/delete-icon.png" style="width: 20px; cursor:pointer;" title="Удалить" >
                                      </template>
                                  </td>

                              </tr>
                          </table>
                        </li>


                  </template>
                  <template v-else-if="commonActionName == 'tables'" >

                        <li  class="filter" style="display: block; text-align: left; background: none; margin:0px; padding:0px" >
                            <table class="table commonLeftTable" >
                                <tr v-for="(item, tableName) in tableList" >

                                    <td @click="commonForm(item)"
                                        class="commonLeftTableTd" style="width:91%; color:green; font-style: italic; " >
                                        <div >{{tableName}}</div>
                                    </td>
                                    <!-- <td class="commonLeftTableTd" style="width:11%;" >{{tableName}}</td>-->
                                    <td class="commonLeftTableTd" style="background: none; text-align:center" >
                                        <img @click="commonDeleteTable(tableName)"
                                             src="images/delete-icon.png" style="width: 20px; cursor:pointer;" title="Удалить" >
                                    </td>

                                </tr>
                            </table>
                        </li>

                  </template>
              </ul>
        </div>

        <!--  ПРАВЫЙ БЛОК  --->
        <div class="col-md-7 commonRightPanel" >

            <h4 style="text-align:center">{{commonItemName}}</h4>

            <pre v-if="dbRoles.length" >{{dbRoles}}</pre>

            <pre v-if="freeSqlCommandResult.length" >
                {{freeSqlCommandResult}}
            </pre>

            <template v-if="commonActionName == 'users'" >
                <div v-for="(item, fname) in commonItem" style="display: flex; padding:3px;">
                    <label style="font-style: italic; width:30%; color:lightseagreen; padding-left:6px;" >{{fname}}</label>
                    <input v-model="commonItem[fname]" style="font-style: italic; width:100%;"
                           type="text" class="form-control" :placeholder="fname">
                </div>
            </template>
            <template v-else-if="commonActionName == 'databases'" >
                <div v-for="(item, fname) in commonItem" style="display: flex; padding:3px;">
                    <label style="font-style: italic; width:30%; color:lightseagreen; padding-left:6px;" >{{fname}}</label>
                    <input v-model="commonItem[fname]" style="font-style: italic; width:100%;"
                           type="text" class="form-control" :placeholder="fname">
                </div>
            </template>
            <template v-else-if="commonActionName == 'tables'" >
                <div v-for="(item, fname) in commonItem" >
                    <!-- <label style="font-style: italic; width:30%; color:lightseagreen; padding-left:6px;" >
                        {{fname}}</label>-->

                    <template v-if="commonItem[fname]['auto_increment']">
                        <div style="display: flex; padding:3px; height:40px;" >
                            <div style="width:100%; height:100%;" >
                                <input v-model="commonItem[fname]['column_name']" style="font-style: italic; width:100%; height:100%;"
                                       type="text" class="form-control" :placeholder="fname" disabled>
                            </div>
                        </div>
                    </template>
                    <template v-else >
                        <div style="display: flex; padding:3px; height:40px;" >

                            <!-- <div style="" >rtyy</div>-->

                            <div style="width:65%; height:100%;" >
                                <input @change="changeFieldName(fname, commonItem[fname]['column_name'])"
                                       v-model="commonItem[fname]['column_name']" style="font-style: italic; width:100%; height:100%;"
                                       type="text" class="form-control" :placeholder="fname" >
                            </div>
                            <div style="width:30%; height:100%; margin:0px 8px 0px 5px; " >
                                <select v-model="commonItem[fname]['input_type']"
                                        @change="changeFieldType(fname, commonItem[fname]['input_type'])" class="form-control"
                                        style="font-style: italic; width:100%; height: 100%; height:100%;" >
                                        <option v-for="(item, i) in tableFieldTypes" :value="item.name" >
                                            {{item.name}}
                                        </option>
                                </select>
                            </div>
                            <div style="width:5%; height:100%; text-align:right" >
                                <img @click="commonDeleteField(fname)" src="images/delete-icon.png"
                                     style="width: 100%; height:100%; cursor:pointer;" title="Удалить" >
                            </div>
                        </div>
                    </template>

                    <!---
                    <div style="display: block;" >
                        <pre>{{commonItem[fname]}}</pre>
                    </div>
                    --->

                </div>
            </template>
            <template v-else-if="commonActionName == 'form-edit-com'" >

                <form-edit-com
                    :data="itemTest"
                    :form_model="itemTestModel"
                    :select_items="{'dbname' : selectTestDb, 'user' : selectTestUser}"
                    @get_form_data="getFormDataTest"
                ></form-edit-com>

            </template>

        </div>

    </div>

</div>


<modal-form modal_id="add-new-table-modal" >
    <template slot="title" ><h4>Добавить таблицу</h4></template>
    <template slot="content" style="width:50%;" >
        <div>
            <div style="text-align:left" >
                <label style="font-style: italic;" >Имя таблицы</label>
                <input v-model="newTable.name" type="text" class="form-control" placeholder="Имя таблицы">
            </div>

            <div style="text-align:left">
                <label style="font-style: italic;" >Задать Id</label>
                <input v-model="newTable.idName" type="text" class="form-control" placeholder="Id">
            </div>

            <div style="margin-top:20px;">
                <button @click="createTable()" class="btn customBtn"
                        data-dismiss="modal"
                        style="width: 100%; margin:0px; border-radius: 0px;"  >
                    Сохранить
                </button>
            </div>
        </div>
    </template>
</modal-form>

<modal-form modal_id="add-new-field-modal" >
    <template slot="title" ><h4>Добавить поле в таблице</h4></template>
    <template slot="content" style="width:50%;" >
        <div>

            <div style="text-align:left">
                <label style="font-style: italic;">Имя поля</label>
                <input v-model="newField.name" type="text" class="form-control" placeholder="Имя поля">
            </div>

            <div style="text-align:left">
                <label style="font-style: italic;">Тип поля</label>
                <select v-model="newField.type" class="form-control" placeholder="Тип поля" style="cursor:pointer;">
                    <option v-for="(item, i) in tableFieldTypes" :value="item.name" >
                        {{item.name}}
                    </option>
                </select>
            </div>

            <div style="margin-top:20px;">
                <button @click="addField()" class="btn customBtn"
                        data-dismiss="modal"
                        style="width: 100%; margin:0px; border-radius: 0px;"  >
                        Сохранить
                </button>
            </div>

        </div>
    </template>
</modal-form>


<modal-form modal_id="add-new-db-modal" >
    <template slot="title" ><h4>Добавить базу</h4></template>
    <template slot="content" style="width:50%;" >
        <div>

            <div style="text-align:left" >
                <label style="font-style: italic;">Имя базы</label>
                <input v-model="newDbName" type="text"
                       class="form-control" placeholder="Имя базы" >
            </div>

            <div style="margin-top:20px;">
                <button @click="addNewDb()" class="btn customBtn"
                        data-dismiss="modal"
                        style="width: 100%; margin:0px; border-radius: 0px;" >
                        Сохранить
                </button>
            </div>

        </div>
    </template>
</modal-form>


<modal-form modal_id="add-new-user-modal" >
    <template slot="title" ><h4>Добавить пользователя</h4></template>
    <template slot="content" style="width:50%;" >
        <div>

            <div style="text-align:left" >
                <label style="font-style: italic;" >Имя пользователя</label>
                <input v-model="newUser.name" type="text" class="form-control" placeholder="Имя пользователя">
            </div>

            <div style="text-align:left" >
                <label style="font-style: italic;" >Пароль</label>
                <input v-model="newUser.password" type="text" class="form-control" placeholder="Пароль">
            </div>

            <div style="margin-top:20px;">
                <button @click="createDbUser()"
                        data-dismiss="modal" class="btn customBtn"
                        style="width: 100%; margin:0px; border-radius: 0px;" >
                        Сохранить
                </button>
            </div>

        </div>
    </template>
</modal-form>


<modal-form modal_id="set-db-privilegies" >
    <template slot="title" ><h4>Привязать пользователя к базе</h4></template>
    <template slot="content" style="width:50%;" >
        <div>

            <div style="text-align:left">
                <label style="font-style: italic;" >Имя пользователя</label>
                <input v-model="userName" type="text" class="form-control" placeholder="Имя пользователя">
            </div>

            <div style="text-align:left" >
                <label style="font-style: italic;">Выбрать базу</label>
                <select v-model="selectDbName" class="form-control">
                    <option v-for="(item) in databaseList"
                            :value="item.datname">{{item.datname}}</option>
                </select>
            </div>

            <div v-if="userName" style="margin-top:20px;">
                <button @click="setUserPrivileges(userName, selectDbName)"
                        data-dismiss="modal" class="btn customBtn"
                        style="width: 100%; margin:0px; border-radius: 0px;" >
                        Изменить
                </button>
            </div>

        </div>
    </template>
</modal-form>

<modal-form modal_id="delele-db-privilegies" >
    <template slot="title" ><h4 style="color:red" >Отвязать пользователя от базы</h4></template>
    <template slot="content" style="width:50%;" >
        <div>

            <div style="text-align:left" >
                <label style="font-style: italic;" >Имя пользователя</label>
                <input v-model="userName" type="text" class="form-control" placeholder="Имя пользователя">
            </div>

            <div style="text-align:left" >
                <label style="font-style: italic;">Выбрать базу</label>
                <select v-model="selectDbName" class="form-control">
                    <option v-for="(item) in databaseList"
                            :value="item.datname">{{item.datname}}</option>
                </select>
            </div>

            <div style="margin-top:20px;">
                <button @click="delUserPrivileges(userName, selectDbName)"
                        data-dismiss="modal" class="btn customBtn"
                        style="width: 100%; margin:0px; border-radius: 0px;" >
                        Изменить
                </button>
            </div>

        </div>
    </template>
</modal-form>

<!--<modal-form modal_id="get-db-dump" >-->
<!--    <template slot="title" ><h4>Привязать базу</h4></template>-->
<!--    <template slot="content" style="width:50%;" >-->
<!--        <div>-->
<!--           -->
<!---->
<!--        </div>-->
<!--    </template>-->
<!--</modal-form>-->


