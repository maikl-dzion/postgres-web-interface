<div class="container">

    <!-- ЗАГОЛОВОК -->
    <div class="row" style="border-bottom: 1px gainsboro solid; padding:0px;margin:0px;">
        <div class="col-sm-6" style="padding: 0px;">
            <h4 style="font-weight: bold; margin: 0px">Управление данными</h4>
        </div>
        <div class="col-sm-3">
            <h4 style="margin:0px; color:#dd4b39; font-weight: bold;"><{{tableName}}></h4>
        </div>
        <div class="col-sm-3"
             style="text-align: right; font-weight: bold; cursor:pointer; padding: 0px;">
            <button class="btn" @click="deleteTable()" style="font-weight: bold;border-radius: 0px">Удалить таблицу
            </button>
        </div>
    </div>

    <div class="row">
        <!-- ЛЕВЫЙ БЛОК -->
        <div class="col-md-2" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a; "><h4>Список таблиц</h4></div>
        <!-- СРЕДНИЙ БЛОК -->
        <div class="col-md-10" style="border-bottom: 0px solid #f7639a;"><h4 style="text-align:center">Данные таблицы
                "{{tableName}}"</h4></div>
        <!-- ПРАВЫЙ БЛОК -->
        <!--                <div class="col-md-3" style="border:0px gainsboro solid; border-bottom: 4px solid #f7639a;"><h4 style="text-align:center">Поля таблицы</h4></div>-->
    </div>

    <!--<pre>{{tableListSheme}}</pre>-->

    <!-- КОНТЕНТ -->
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
        <div class="col-md-10" style="margin:0px; padding:0px;">

            <div style="display: flex;">
                <div class="contact-us-detail"
                     style="cursor:pointer; text-align:center;position: relative; top:0px; left:0px; margin:2px 0px 4px 0px; border-radius: 0px; width: 30%">
                    <a @click="addItem()" style="text-align:center; display: block">Добавить новую запись в базу</a></div>

                <div class="contact-us-detail"
                     style="cursor:pointer; text-align:center;position: relative; top:0px; left:0px; margin:2px 0px 4px 5px; border-radius: 0px; width: 20%">
                    <a @click="dataShowType = 'row'" style="text-align:center; display: block">В линию</a></div>

                <div class="contact-us-detail"
                     style="cursor:pointer; text-align:center;position: relative; top:0px; left:0px; margin:2px 0px 4px 5px; border-radius: 0px; width: 20%">
                    <a @click="dataShowType = 'line'" style="text-align:center; display: block">В ряд</a></div>
            </div>

            <div >

                <template v-if="dataShowType == 'row'" >

                    <table class="data-table-show" style="width:100%;">
                        <tr>
                            <th style="width:15px !important">
                                <div style="text-align:center; background: gainsboro;padding:5px; border-right: 0px grey solid;">X</div>
                            </th>
                            <th v-for="(values, fieldName) in tableInfo" style="border:1px grey solid" >
                                <div style="text-align:center; background: gainsboro;
                                                padding:5px; border: 0px grey solid; color:rgb(247, 99, 154)">{{fieldName}}</div>
                            </th>
                        </tr>

                        <tr v-for="(item, i) in tableData">
                            <td style="width:15px !important; padding:0px 0px 0px 3px; height:100%;">
                                <div @click="deleteItem(item)" title="Удалить запись" style="text-align:center; cursor:pointer; height:100%; border:0px grey solid;"> X </div>
                            </td>
                            <template v-for="(info, fieldName) in tableInfo">
                                <td style="padding:0px; margin:0px; background: white">
                                    <div style="text-align:center; background: white;width: 100%;height: 100%; padding:5px; border: 1px ghostwhite solid;">
                                        <input v-if="!info.auto_increment" @input="editItem(fieldName, item)"
                                               v-model="item[fieldName]" type="text" class="form-control">
                                        <input v-else v-model="item[fieldName]" type="text" class="form-control" disabled>
                                    </div>
                                </td>
                            </template>
                        </tr>
                    </table>

                </template>
                <template v-else >


                        <div v-for="(item, i) in tableData" style="margin-bottom:10px;">

                            <div style="width:15px !important; padding:0px 0px 0px 3px; height:100%;">
                                <div @click="deleteItem(item)" title="Удалить запись" style="text-align:center; cursor:pointer; height:100%; border:0px grey solid;"> X </div>
                            </div>

                            <template v-for="(info, fieldName) in tableInfo">
                                <div style="display:flex; padding:0px; margin:0px; background: white">
                                    <div style="width:20%; padding:4px;">{{fieldName}}</div>

                                    <div style="text-align:center; background: white; width:80%; height: 100%; padding:5px; border: 1px ghostwhite solid;">
                                        <input v-if="!info.auto_increment" @input="editItem(fieldName, item)"
                                               v-model="item[fieldName]" type="text" class="form-control">
                                        <input v-else v-model="item[fieldName]" type="text" class="form-control" disabled>
                                    </div>
                                </div>
                            </template>
                            <hr>
                        </div>


                </template

            </div>

        </div>
        <!--  СРЕДНИЙ БЛОК END ---->
        <!-- ################### -->

    </div>

</div>
