
const App = new Vue({
    el : '#app',
    // router : router,
    mixins : [Http],
    data: function () {

        var fieldModel = {
            name : '',
            type : 'VARCHAR',
            size : '',
        };

        return  {
            tableName : '',
            tableList : [],
            tableInfo : [],
            tableData : [],
            newFieldsJson : [],

            newField : fieldModel,
            newFieldModel : fieldModel,

            renameName : {
                oldName : '',
                newName : '',
            },

            newTable : {
                name : '',
                idName : '',
            },

            showModalOne: false,
            showModalConf : false,

            dbConf : {
                'host'    : ''
                ,'dbname' : ''
                ,'user'   : ''
                ,'passwd' : ''
                ,'driver' : 'pgsql'
                ,'port'   : 5432
            },
        }
    },

    computed : {

    },

    created() {
        // this.getTableList(this.getTableFields);
        this.http('GET_TABLE_LIST').then(resp => {
            this.tableList = resp;
            for(var name in resp) {
                this.getTableFields(name);
                return;
            }
        });

        // this.saveConfig();
    },

    methods: {

        // Получить все таблицы
        getTableList() {
            var url = 'GET_TABLE_LIST';
            this.http(url).then(resp => {
                this.tableList = resp;
                // lg(resp)
            });
        },

        // Получить поля таблицы
        getTableFields(tableName) {
            this.tableInfo = [];
            this.tableName = tableName;
            var url = 'GET_TABLE_FIELDS/' + this.tableName;
            this.http(url).then(resp => {
                this.tableInfo = resp;
                this.getTableData(this.tableName);
            });
        },

        getTableData(tableName) {
            var url = 'GET_TABLE_DATA/' + tableName;
            this.http(url).then(resp => {
                this.tableData = resp;
            });
        },

        addField() {
            var name = this.newField.name;
            var type = this.newField.type;
            if(!name) return false;

            var url = 'ADD_FIELD/' + this.tableName + '/' + name + '/' + type;
            this.http(url).then(resp => {
                this.getTableFields(this.tableName);
                this.alertSuccess('Новое поле успешно добавлено');
            });
        },

        multipleAddField() {
            for(var i in this.newFieldsJson) {
                var field = this.newFieldsJson[i];
                if(!field.name) continue;
                var url = 'ADD_FIELD/' + this.tableName + '/' + field.name + '/' + field.type;
                this.http(url).then(resp => {
                    // this.alertSuccess('Новое поле успешно добавлено');
                });
            }

            setTimeout(() => {
                this.getTableFields(this.tableName);
                this.alertSuccess('Новые поле успешно добавлены');
                this.showModalOpen();
            }, 1000);

            // this.getTableFields(this.tableName);
        },

        showModalOpen() {
            this.newFieldsJson = [];
            if(this.showModalOne){
                this.showModalOne = false;
            } else {
                this.addModalField();
                this.showModalOne = true;
            }
        },

        confModalOpen() {
            if(this.showModalConf){
                this.showModalConf = false;
            } else {
                this.showModalConf = true;
            }
        },

        addModalField() {
            this.newFieldsJson.push(Object.assign({}, this.newFieldModel));
        },

        deleteField(fieldName) {
            var url = 'DELETE_FIELD/' + this.tableName + '/' + fieldName;
            this.http(url).then(resp => {
                this.getTableFields(this.tableName);
                // this.alertSuccess('Поле удалено');
            });
        },

        deleteTable() {
            var url = 'DELETE_TABLE/' + this.tableName;
            this.http(url).then(resp => {
                this.tableName = '';
                this.http('GET_TABLE_LIST').then(resp => {
                    this.tableList = resp;
                    for(var name in resp) {
                        this.getTableFields(name);
                        return;
                    }
                });
            });
        },

        createTable() {
            var tableName = this.newTable.name;
            var idName = this.newTable.idName;
            if(!tableName) return false;

            var url = 'CREATE_TABLE/' + tableName + '/' + idName;
            this.http(url).then(resp => {
                this.tableName = tableName;
                this.getTableList();
                this.getTableFields(this.tableName);
                this.alertSuccess('Новая таблица успешно создана');
            });
        },

        initRenameField(fieldName) {
            var name = this.tableInfo[fieldName]['column_name'];
            this.renameName.oldName = fieldName;
        },

        renameField(fieldName) {
            var name = this.tableInfo[fieldName]['column_name'];
            this.renameName.newName = name;

            var newName = this.renameName.newName;
            var oldName = this.renameName.oldName;

            if(oldName == newName) return;

            var url = 'RENAME_FIELD/' + this.tableName + '/' + oldName + '/' + newName;
            this.http(url).then(resp => {
                this.getTableFields(this.tableName);
            });
        },

        saveConfig() {
            var url = 'SAVE_CONFIG';
            var postData = this.dbConf;
            this.http(url, postData, 'post').then(resp => {
                this.getTableList();
                // lg(resp)
            });
        },

        alertSuccess(text) {
            alert(text);
        },
    },

});



function lg(arr) {

    var result = '---NOT ARRAY---';
    if (arr) result = _printf(arr); // --- формируем строку из массива
    _openWindow(result);            // --- показываем результат в новом окне

    // --- формируем строку из массива
    function _printf(arr) {
        var strResult = '', deLimiter = ' => ';
        var typeObj = typeof(arr);
        if (typeObj == 'object') {
            for (var i in arr) {

                var values = arr[i];
                var subValues = '';
                var li = deLimiter;
                if (typeof(values) == 'object') {
                    subValues = _printf(values);
                } else {
                    li += values;
                }

                strResult += '<li>[' + i + ']' + li + '</li>' + subValues;
            }
        }
        else strResult = '<li>' + arr + '</li>';

        return '<ul>' + strResult + '</ul>';
    }

    // --- показываем результат в новом окне
    function _openWindow(result, href) {
        var modal = window.open('', '', 'scrollbars=1');
        var style = 'button { padding:10px; margin:10px; border:0px grey solid; width:30%; cursor:ponter  }'
            + ' .lg-view-result { border:2px red solid; }';
        var html = '<!DOCTYPE html><head><style>' + style + '</style><head>'
            + '<p><button onclick="window.close();" >Close</button></p><hr>'
            + '<p class="lg-view-result" ><pre>' + result + '</pre></p>';
        modal.document.body.innerHTML = html;
    }
}