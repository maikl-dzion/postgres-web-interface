<div class="container">

    <div class="row" style="border-bottom: 1px gainsboro solid; padding:0px;margin:0px;">
        <div class="col-sm-6" style="padding: 0px;">
            <h4 style="font-weight: bold; margin: 0px">Выбранная база</h4>
        </div>
        <div class="col-sm-3">
            <h4 v-if="selectDbName" style="margin:0px; color:#dd4b39; font-weight: bold;">
                <{{selectDbName}}>
            </h4>
        </div>
        <div class="col-sm-3"
             style="text-align: right; font-weight: bold; cursor:pointer; padding: 0px;">
            <button @click="deleteDb()" class="btn" style="font-weight: bold;border-radius: 0px">Удалить базу</button>
        </div>
    </div>

    <current-info :data_info="curUserInfo" ></current-info>

<!--    <div class="row" style="border-bottom: 1px gainsboro solid; padding:0px;margin:0px;">-->
<!--        <div v-for="(user) in currentDbUser" style="display: flex">-->
<!--            <div>база : <{{user.datname}}></div>-->
<!--            <div style="margin-left:10px;">пользователь : <{{user.usename}}></div>-->
<!--            <div style="margin-left:10px;">ip-адрес : <{{user.client_addr}}></div>-->
<!--            <div style="margin-left:10px;">порт : <{{user.client_port}}></div>-->
<!--        </div>-->
<!--    </div>-->

    <div class="row">
        <!-- ЛЕВЫЙ БЛОК -->
        <div class="col-md-2" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a; "><h4>Список баз</h4>
        </div>
        <!-- СРЕДНИЙ БЛОК -->
        <div class="col-md-7" style="border-bottom: 0px solid #f7639a;"><h4 style="text-align:center">Схема базы
                данных</h4></div>
        <!-- ПРАВЫЙ БЛОК -->
        <div class="col-md-3" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a;"><h4
                    style="text-align:center">Поля таблицы</h4></div>
    </div>

    <div class="row">

        <!-- ################### -->
        <!-- ЛЕВЫЙ БЛОК ----------->
        <div class="col-md-2" style="padding:0px 2px 2px 2px; border:0px gainsboro solid; min-height: 400px;">
            <div class="contact-us-detail"
                 style="cursor:pointer; text-align:center;position: relative; top:0px; left:0px; margin:2px 0px 4px 0px; border-radius: 0px; width: 100%">
                <a @click="toggleVar('addDbFlag')" style="text-align:center; display: block">Добавить базу</a></div>

            <div v-if="addDbFlag" class="" style="border:1px gainsboro solid; padding:3px;
                       box-shadow: 0px 1px 2px 0px rgba(90, 91, 95, 0.15);
                       transition: all 0.3s ease-in-out; text-align:center">
                <div class="divider dark"><i class="icon-picture"></i></div>
                <div>
                    <label style="font-style: italic;">Имя базы</label>
                    <input v-model="newDbName" type="text" class="form-control" placeholder="Имя базы">
                </div>
                <br>
                <button @click="addNewDb()" class="btn btn-primary" style="border-radius: 0px; width: 100%">
                    Сохранить
                </button>
            </div>

            <ul class="portfolio" style="padding:0px; margin:0px;">
                <template v-for="(item) in databaseList">
                    <li @click="selectDbItem(item)" class="filter" style="display: block; text-align: left">
                        {{item.datname}}
                        <div style="display: none">
                            <pre>{{item}}</pre>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
        <!-- </ ЛЕВЫЙ БЛОК -------->
        <!-- ################### -->
        <!-- СРЕДНИЙ БЛОК START --->
        <div @mousemove="dragMove($event)" class="col-md-7 dragCanvas" style="">

            <template v-for="(item, x) in tableListSheme">
                <div v-if="item.name != 'products'"
                     @mousedown="dragInit($event)"
                     @click="getTableFields(item.name)"
                     @mouseup="dragStop($event)"
                     :id="'move_el_' + item.name" class="table-responsive dragElem"
                     :style="'left:'+item.left+'px; top:'+item.top+'px;'">
                    <table class="table">
                        <tr>
                            <td style="margin:0px; padding:5px;text-align: center">{{item.name}}</td>
                        </tr>
                        <tr v-for="(field, i) in item.fields">
                            <td style="margin:0px; padding:5px;background-color: #4c9cef;color:white;">
                                <div>{{field.name}}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </template>

        </div>
        <!--  СРЕДНИЙ БЛОК END ---->
        <!-- ################### -->
        <!--  ПРАВЫЙ БЛОК  -------->
        <div class="col-md-3" style="padding:0px 2px 2px 2px; border:0px gainsboro solid">

            <div  class="table-responsive" style="" >
                <table class="table">
                    <tr><td >Поле</td><td >Значение</td></tr>
                </table>
            </div>

            <div v-for="(item, fname) in dbItem" class="table-responsive" style="" >
                <table class="table">
                    <tr><td >{{fname}}</td><td >{{item}}</td></tr>
                </table>
            </div>

        </div>
        <!-- </ ПРАВЫЙ БЛОК ------->
        <!-- ################### -->

    </div>

</div>
