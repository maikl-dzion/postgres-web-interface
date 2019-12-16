<div class="container">

    <div class="row" style="border-bottom: 1px gainsboro solid; padding:0px;margin:0px;">
        <div class="col-sm-6" style="padding: 0px;">
            <h4 style="font-weight: bold; margin: 0px">Управление таблицами</h4>
        </div>
        <div class="col-sm-3">
            <h4 style="margin:0px; color:#dd4b39; font-weight: bold;"><{{tableName}}></h4>
            <!--<div>-->
            <!--<h4 style="float:left;margin:0px; color:#dd4b39; font-weight: bold;">{{tableName}}</h4>-->
            <!--<div class="contact-us-detail" style="float:right;position: relative; width:30%;"-->
            <!--&gt;<a href="#">удалить таблицу</a></div>-->
            <!--</div><div style="clear:both"></div>-->
            <!--<div class="table-responsive">-->
            <!--<table class="table">-->
            <!--<tr><td>{{tableName}}</td><td></td></tr>-->
            <!--</table>-->
            <!--</div>-->
        </div>
        <div class="col-sm-3"
             style="text-align: right; font-weight: bold; cursor:pointer; padding: 0px;">
            <button class="btn" @click="deleteTable()" style="font-weight: bold;border-radius: 0px">Удалить таблицу
            </button>
        </div>
    </div>

    <div class="row">
        <!-- ЛЕВЫЙ БЛОК -->
        <div class="col-md-2" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a; "><h4>Список
                таблиц</h4></div>
        <!-- СРЕДНИЙ БЛОК -->
        <div class="col-md-7" style="border-bottom: 0px solid #f7639a;"><h4 style="text-align:center">Схема таблиц</h4>
        </div>
        <!-- ПРАВЫЙ БЛОК -->
        <div class="col-md-3" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a;"><h4
                    style="text-align:center">Поля таблицы</h4></div>
    </div>

    <!--            <pre>{{databaseList}}</pre>-->

    <div style="display: none">
        <div v-for="(db) in databaseList">
            <div>{{db.datname}}</div>
            <div style="display: none">
                <pre>{{db}}</pre>
            </div>
        </div>
    </div>


    <div class="row">

        <!-- ################### -->
        <!-- ЛЕВЫЙ БЛОК ----------->
        <div class="col-md-2" style="padding:0px 2px 2px 2px; border:0px gainsboro solid; min-height: 400px;">
            <div class="contact-us-detail"
                 style="cursor:pointer; text-align:center;position: relative; top:0px; left:0px; margin:2px 0px 4px 0px; border-radius: 0px; width: 100%">
                <a @click="addTableOpen()" style="text-align:center;">Добавить таблицу</a></div>

            <div v-if="addTableFlag" class=""
                 style="border:1px gainsboro solid; padding:3px;
                   box-shadow: 0px 1px 2px 0px rgba(90, 91, 95, 0.15);
                   transition: all 0.3s ease-in-out;">
                <div class="divider dark">
                    <i class="icon-picture"></i>
                </div>
                <label style="font-style: italic;">Имя таблицы</label>
                <input v-model="newTable.name" type="text" class="form-control" placeholder="Имя таблицы">
                <label style="font-style: italic;">Имя ID поля</label>
                <input v-model="newTable.idName" type="text" class="form-control" placeholder="Имя поля"><br>

                <button @click="createTable()" class="btn btn-primary" style="border-radius: 0px; width: 100%">
                    Сохранить таблицу
                </button>
            </div>

            <!--<ul style="list-style-type: none">-->
            <!--<li v-for="(value, tableName) in tableList">-->
            <!--<a v-if="tableName != 'products'" href="#" @click="getTableFields(tableName)"-->
            <!--style="cursor:pointer; font-size: 16px; font-weight: bold">{{tableName}}</a>-->
            <!--</li>-->
            <!--</ul>-->

            <ul class="portfolio" style="padding:0px; margin:0px;">
                <template v-for="(value, tableName) in tableList">
                    <li v-if="tableName != 'products'" @click="getTableFields(tableName)"
                        class="filter" style="display: block; text-align: left">{{tableName}}
                    </li>
                </template>
            </ul>
        </div>
        <!-- </ ЛЕВЫЙ БЛОК -------->
        <!-- ################### -->
        <!-- СРЕДНИЙ БЛОК START --->
        <div @mousemove="dragMove($event)" class="col-md-7 dragCanvas" style="">

            <template v-for="(item, x) in tableListSheme" >

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
            <!--<h4 style="font-style:italic; text-align:center">{{tableName}}</h4>-->

            <div class="contact-us-detail"
                 style="cursor:pointer; text-align:center;position: relative; top:0px; left:0px; margin:2px 0px 4px 0px; border-radius: 0px; width: 100%">
                <a @click="addFieldOpen()" style="text-align:center; display: block">Добавить поле</a></div>

            <div v-if="addFieldFlag"
                 style="border:1px gainsboro solid; padding:6px; margin:3px; box-shadow: 0px 1px 2px 0px rgba(90, 91, 95, 0.15);">
                <div class="divider dark">
                    <i class="icon-picture"></i>
                </div>
                <label style="font-style: italic;">Имя поля</label>
                <input v-model="newField.name" type="text" class="form-control" placeholder="Имя поля">
                <label style="font-style: italic;">Тип поля</label>
                <select v-model="newField.type" class="form-control" placeholder="Тип поля">
                    <option value="INTEGER">Число</option>
                    <option value="VARCHAR">Строка</option>
                    <option value="TEXT">Текст</option>
                </select>

                <button @click="addField()" class="btn btn-primary"
                        style="border-radius: 0px; margin:5px 5px 5px 0px;">Сохранить поле
                </button>
                <br>

            </div>

            <div v-for="(values, fieldName) in tableInfo" :key="fieldName" style="">
                <div style="display: flex; transition: width 2s, height 2s, background-color 2s, transform 2s; ">

                    <div style="width: 88%;">

                        <template v-if="!values.auto_increment">
                            <input v-model="values.column_name"
                                   @focus="initRenameField(fieldName)"
                                   @change="renameField(fieldName)"
                                   type="text" class="form-control" placeholder="">
                        </template>
                        <template v-else>
                            <input v-model="values.column_name"
                                   type="text" class="form-control" placeholder="" disabled>
                        </template>

<!--                        <pre style="display: block;">{{values}}</pre>-->

                    </div>
                    <div style="width: 12%; border:1px grey solid;">
                        <button @click="deleteField(fieldName)"
                                class="btn btn-primary" style="width:100%; height:100%; border-radius: 0px;
                                                 margin:0px; padding:0px;">del
                        </button>
                    </div>
                    <!--<p>{{values}}</p>-->

                </div>
            </div>

        </div>
        <!-- </ ПРАВЫЙ БЛОК ------->
        <!-- ################### -->

    </div>

</div>