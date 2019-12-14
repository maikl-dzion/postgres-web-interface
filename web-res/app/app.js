
Vue.use(VueMaterial.default)

const App = new Vue({
    el: '#app-page',
    // router : router,
    mixins: [Http, BaseMixin, dragMixin],
    data: function () {

        var fieldModel = {
            name: '',
            type: 'VARCHAR',
            size: '',
        };

        return {
            showPanel: true,
            tableName: '',
            tableListSheme: [],
            tableList: [],
            tableInfo: [],
            tableData: [],
            newFieldsJson: [],
            // dbUser : {},

            newField: fieldModel,
            newFieldModel: fieldModel,

            renameName: {
                oldName: '',
                newName: '',
            },

            newTable: {
                name: '',
                idName: 'id',
            },

            showModalOne : false,
            showModalConf: false,
            addFieldFlag : false,
            addTableFlag : false,
            autoIncName  : '',

        }
    },

    computed: {},

    created() {

        // this.getTableIdName('users');
        // this.getTableList(this.getTableFields);
        this.http('GET_TABLE_LIST').then(resp => {
            this.getTableListSheme();
            this.tableList = resp;
            for (var name in resp) {
                this.getTableFields(name);
                return;
            }
        });

        // this.saveConfig();

        // this.showDatabaseList();
        // this.getCurrentDatabase();
        // this.getDbUsers();
    },

    methods: {

        addFieldOpen() {
            (this.addFieldFlag) ? this.addFieldFlag = false :
                this.addFieldFlag = true;
        },

        addTableOpen() {
            (this.addTableFlag) ? this.addTableFlag = false :
                this.addTableFlag = true;
        },

        editItem(fieldName, item) {
            var newValue = item[fieldName];
            var itemId = item['id'];
            var url = 'EDIT_ITEM/' + this.tableName + '/' + fieldName + '/' + itemId + '/' + newValue;
            this.http(url).then(resp => {
                //var r = resp;
                this.getTableListSheme();
                // this.getTableData(this.tableName);
            });
        },

        deleteItem(item) {

            var autoIncName = this.setAutoIncName();

            if (item[autoIncName]) {
                var itemId = item[autoIncName]
                var url = 'DELETE_ITEM/' + this.tableName + '/' + autoIncName + '/' + itemId;
                this.http(url).then(resp => {
                    this.getTableData(this.tableName);
                });
            } else {
                alert('Не найдено поле auto_increment');
            }
        },

        setAutoIncName() {
            var fields = this.tableInfo;
            for (var fName in fields) {
                if (fields[fName].auto_increment)
                    this.autoIncName = fName;
            }
            return this.autoIncName;
        },

        addItem() {
            var url = 'ADD_ITEM/' + this.tableName;
            this.http(url).then(resp => {
                //var r = resp;
                this.getTableData(this.tableName);
                this.getTableListSheme();
            });
        },

        clickOpenPanel() {
            if (this.showPanel) this.showPanel = false;
            else this.showPanel = true;
        },

        // Получить имя поля autoincrement
        getTableIdName(tableName) {
            if (!tableName) tableName = this.tableName;
            var url = 'GET_TABLE_ID_NAME/' + tableName;
            this.http(url).then(resp => {
                this.autoIncName = resp['column_name'];
            });
        },

        // Получить все таблицы
        getTableList() {
            var url = 'GET_TABLE_LIST';
            this.http(url).then(resp => {
                this.tableList = resp;
                this.getTableListSheme();
                // lg(resp)
            });
        },

        // Получить поля таблицы
        getTableFields(tableName, callback = null) {
            this.tableInfo = [];
            this.tableName = tableName;
            var url = 'GET_TABLE_FIELDS/' + this.tableName;
            // this.getTableIdName(tableName);
            this.http(url).then(resp => {
                if(callback) {
                    callback(resp);
                    return;
                }
                this.tableInfo = resp;
                this.getTableData(this.tableName);

            });
        },

        // Получить все таблицы с полями
        getTableListSheme() {
            var url = 'GET_TABLE_LIST_SHEME';
            this.http(url).then(resp => {
                var ch = 0;
                var _left = 0
                var _top = 0;
                for (var i in resp) {
                    if (ch > 5)
                        _left = _top = ch = 0;

                    if (ch > 0) {
                        _left = _left + 45;
                        _top = _top + 30;
                    }

                    resp[i]['top'] = _top;
                    resp[i]['left'] = _left;

                    ch++;
                }
                this.tableListSheme = resp;
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
            if (!name) return false;

            var url = 'ADD_FIELD/' + this.tableName + '/' + name + '/' + type;
            this.http(url).then(resp => {
                this.getTableFields(this.tableName);
                this.addFieldFlag = false;
                this.alertSuccess('Новое поле успешно добавлено');
                this.getTableListSheme();
            });
        },

        multipleAddField() {
            for (var i in this.newFieldsJson) {
                var field = this.newFieldsJson[i];
                if (!field.name) continue;
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
            if (this.showModalOne) {
                this.showModalOne = false;
            } else {
                this.addModalField();
                this.showModalOne = true;
            }
        },

        confModalOpen() {
            if (this.showModalConf) {
                this.showModalConf = false;
            } else {
                this.showModalConf = true;
            }
        },

        addModalField() {
            this.newFieldsJson.push(Object.assign({}, this.newFieldModel));
        },

        deleteField(fieldName, ret = false) {
            var url = 'DELETE_FIELD/' + this.tableName + '/' + fieldName;
            if(ret) return this.http(url);

            this.http(url).then(resp => {
                this.getTableFields(this.tableName);
                this.getTableListSheme();
                // this.alertSuccess('Поле удалено');
            });
        },

        deleteTable() {
            var url = 'DELETE_TABLE/' + this.tableName;
            this.http(url).then(resp => {
                this.tableName = '';
                this.getTableListSheme();
                this.http('GET_TABLE_LIST').then(resp => {
                    this.tableList = resp;
                    for (var name in resp) {
                        this.getTableFields(name);
                        return;
                    }
                });
            });
        },

        createTable() {
            var tableName = this.newTable.name;
            var idName = this.newTable.idName;
            if (!tableName) return false;

            var url = 'CREATE_TABLE/' + tableName + '/' + idName;
            this.http(url).then(resp => {
                this.tableName = tableName;
                this.getTableList();
                this.getTableFields(this.tableName);
                this.getTableListSheme();
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

            if (oldName == newName) return;

            var url = 'RENAME_FIELD/' + this.tableName + '/' + oldName + '/' + newName;
            this.http(url).then(resp => {
                this.getTableListSheme();
                this.getTableFields(this.tableName);
            });
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