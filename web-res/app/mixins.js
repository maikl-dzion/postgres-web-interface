
const BaseMixin = {

    data: function () {

        var itemTest = {
            'user'     : 'w1user',
            'password' : '1234567',
            'dbname'   : 'maikldb',
            'status'   : true,
            'comment'  : '',
        };

        var itemTestModel = {
              'user'    : { type : 'select',    pos : 'row', title : 'Пользователь', width: '35%', value : ''},
              'password': { type : 'text',      pos : '', title : 'Пароль',       width: '35%', value : ''},
              'status'  : { type : 'checkbox',  pos : 'end', title : 'Статус',       width: '30%', value : ''},
              'comment' : { type : 'textarea',  pos : '', title : 'Комментарий',  width: '100%', value : ''},
              'dbname'  : { type : 'select',    pos : '',    title : 'Выбрать базу', width: '100%', value : ''},
        };

        var selectTestDb = [
           { id : 'maikldb',    name : 'maikldb', },
           { id : 'reestrsrv',  name : 'reestrsrv', },
           { id : 'reestrdb',   name : 'reestrdb', },
        ];

        var selectTestUser = [
            { id : 'w1user',      name : 'w1user', },
            { id : 'maikl_super', name : 'maikl_super', },
            { id : 'test_user',   name : 'test_user', },
        ];


        var dbConf = {
              'host': ''
            , 'dbname': ''
            , 'user': ''
            , 'passwd': ''
            , 'driver': 'pgsql'
            , 'port': 5432
        };

        var newUser = {
            name     : '',
            password : '',
            dbName   : '',
            superUser : false,
        };

        return {

            alertMessageShow : false,
            alertMessage     : '',

            commonActionName : 'users',
            commonTitle : 'Пользователи',
            commonItem : [],
            commonItemName : '',

            usersList : [],
            userName  : '',
            userPassword : '',
            userItem : {},
            currentDbUser : [],
            fileUsersConfig : [],
            curUserInfo   : {},
            addUserFlag   : false,

            currentDatabase : '',
            selectDbName : '',
            newDbName : '',
            dbItem : {},
            databaseList    : [],
            curConfig : {},
            addDbFlag : false,

            newUser,
            dbConf,
            dbConfPrint : [],

            remoteUrl : '',

            itemTestModel,
            itemTest,
            selectTestDb,
            selectTestUser,

            sqlCommand : '',
            sqlCommandType : 'query',
            freeSqlCommandResult : [],

            dbRoles : [],

            dataShowType : 'row',

            selectFieldType : 'VARCHAR',

            tableFieldTypes : [
                { name : 'integer', title : 'Число'   , size : '' },
                { name : 'varchar', title : 'Строка'  , size : '255' },
                { name : 'text'   , title : 'Textarea', size : ''  },
            ],

        }
    },  // Data

    created() {

        this.getDbUsersList();   // usersList
        this.getCurrentDbUser(); // currentDbUser

        this.showDatabaseList(); // databaseList
        this.getCurrentDatabase(); // currentDatabase

        this.getCurConfig();       // получаем текущий конфиг
        // this.getFileUsersConfig(); // пользователи

        // this.newUser.dbName = this.currentDatabase;
        // setUserPrivileges/w1user/reestrsrv   установить привилегии к базе
        // setSuperUser/w1user  установить супер статус 
    },

    methods: {

        //######################################
        // Общие Функции

        // переключение переменных
        toggleVar(varName) {
             if(this[varName]) this[varName] = false;
             else this[varName] = true;
        },

        alertMessageClose() {
            this.alertMessageShow = false;
            this.alertMessage = '';
        },

        alertMessageOpen(message) {
            this.alertMessageShow = true;
            this.alertMessage = message;
        },

        alertOpen(message) {
            this.alertMessageShow = true;
            this.alertMessage = message;
        },

        alertSuccess(text) {
            alert(text);
        },

        getFormDataTest(data) {
            let item = data.item;
            let fname = data.fname;
            this.itemTest = item;
        },

        sqlCommandRun(type) {
            var command = "";
            var paramName = '';
            switch(type) {
                case 'get_roles'  :
                    command = "SELECT * FROM pg_roles";
                    paramName = 'dbRoles';
                    break;
            }

            if(command) {
                this.sqlCommand = command;
                this.execSqlCommand(paramName);
            }

        },

        sqlQueryResult(resp) {
            this.freeSqlCommandResult = resp.result;
        },

        sqlButtonClick() {
            if(this.sqlCommand) {
                this.execSqlCommand('freeSqlCommandResult');
            }
        },

        // Выполняем sql команды
        execSqlCommand(paramName = '', callback = null) {
            var url = 'EXEC_SQL_COMMAND/' + this.sqlCommand + '/' + this.sqlCommandType + '/' + this.tableName;
            this.http(url).then(resp => {
                if(paramName)
                   this[paramName] = resp;
                if(callback)
                    callback(resp);
            });
        },

        // устанавливаем настройки по умолчанию
        setDefaultConfig() {
            var url = 'SET_DEFAULT_CONFIG';
            this.http(url).then(resp => {
                var res = resp;
            });
        },

        commonAction(actionName) {
            this.commonItem = [];
            this.dbRoles    = [];
            this.commonItemName = this.freeSqlCommandResult = '';
            this.commonActionName = actionName;
            var title = 'Левая панель';
            switch(this.commonActionName) {
                case 'users'     : title = 'Пользователи'; break;
                case 'tables'    : title = 'Таблицы';      break;
                case 'databases' : title = 'Базы';         break;
            }
            this.commonTitle = title;
        },

        commonForm(item, fname = '') {

            var itemName    = '';
            this.commonItem = item;

            switch(this.commonActionName) {
                case 'users' :
                    this.userName = itemName = item.usename;
                    break;

                case 'databases' :
                    this.selectDbName = itemName = item.datname;
                    break;

                case 'tables' :
                    this.tableName = itemName = item.table_name;
                    this.getTableFields(this.tableName, resp => {
                        this.commonItem = resp;
                    });
                    break;
            }

            this.commonItemName = itemName;
        },

        commonDeleteField(fieldName) {
            var url = 'DELETE_FIELD/' + this.tableName + '/' + fieldName;
            this.deleteField(fieldName, true).then(resp => {
                this.getTableFields(this.tableName, resp => {
                   this.commonItem = resp;
                   this.alertMessageOpen('Успешное удаление поля');
                });
            });
        },

        commonDeleteTable(tableName = '') {

            if(!tableName) {
                if(!this.tableName) {
                    alert('Не выбрана таблица');
                    return false;
                }

                tableName = this.tableName;
            }

            var url = 'DELETE_TABLE/' + tableName;
            this.http(url).then(resp => {
                this.tableName = '';
                this.getTableListSheme();
                this.http('GET_TABLE_LIST').then(resp => {
                    this.tableList = resp;
                    this.commonItem = [];
                    this.alertMessageOpen('Успешное удаление таблицы');
                });
            });
        },

        changeFieldName(fieldName, newName) {
            var url = 'RENAME_FIELD/' + this.tableName + '/' + fieldName + '/' + newName;
            this.http(url).then(resp => {
                this.getTableFields(this.tableName, resp => {
                    this.commonItem = resp;
                    this.alertMessageOpen('Успешное изменение поля');
                });
            });
        },

        changeFieldType(fieldName, newType) {
            var url = 'changeFieldType/' + this.tableName + '/' + fieldName + '/' + newType;
            // alert(url);
            this.http(url).then(resp => {
                this.getTableFields(this.tableName, resp => {
                   this.commonItem = resp;
                   this.alertMessageOpen('Успешное изменение поля');
                });
            });
        },

        checkName(item, fname, message) {
            if(!item) {
                if(!this[fname]) {
                    alert(message);
                    return false;
                }
                item = this[fname];
            }
            return item;
        },

        //######################################
        // Функции для управлениями пользователя

        // все пользователи
        getDbUsersList() {
            var url = 'getDbUsersList';
            this.http(url).then(resp => {
                this.usersList = resp;
                this.getFileUsersConfig(this.combineUsersParams);
            });
        },

        combineUsersParams(files) {
             var fileUsers = files;
             var users = this.usersList;
             for(let i in users) {
                 let userName = users[i]['usename'];
                 let item = users[i];
                 if(userName in fileUsers) {
                     let us = fileUsers[userName];
                     let passwd = us['passwd'];
                     for(var f in us) {
                         item[f] = us[f];
                     }
                     users[i] = item;
                 }
             }
             this.usersList = users;
        },

        getCurrentDbUser() {
            var url = 'CURRENT_DB_USER';
            this.http(url).then(resp => {
                this.currentDbUser = resp;
                let len = this.currentDbUser.length;
                this.curUserInfo = this.currentDbUser[len-1];
            });
        },

        getFileUsersConfig(callback = null) {
            var url = 'GET_FILE_USERS_CONFIG';
            this.http(url).then(resp => {
                this.fileUsersConfig = resp;
                if(callback) callback(this.fileUsersConfig);
            });
        },

        selectUserPassword(userName) {
            let users = this.usersList;
            for(let i in users) {
                let name = users[i]['usename'];
                if(userName == name) {
                    let passwd = users[i]['passwd'];
                    this.dbConf['passwd'] = passwd;
                    return true;
                }
            }
        },

        createDbUser() {
            var funcName = 'createUser';
            let name     = this.newUser.name;
            let password = this.newUser.password;
            let dbname   = this.newUser.dbName;
            let superState   = this.newUser.superUser;
            if(!dbname) dbname = this.currentDatabase;
            var url = funcName + '/' + name + '/' + password + '/' + dbname + '/' + superState ;
            this.http(url).then(resp => {
                this.getDbUsersList();
            });
        },

        setUserName(item) {
            this.userItem = item;
            this.userName = item.usename;
        },

        deleteDbUser(userName = '') {
            let message = 'Не выбран пользователь';
            let name = this.checkName(userName, 'userName', message);
            if(!name) return false;

            var url = 'deleteDbUser/' + name;
            this.http(url).then(resp => {
                this.userName = '';
                this.alertMessageOpen('Успешное удаление пользователя');
                this.getDbUsersList();
            });
        },

        // Установить привилегии к базе
        setUserPrivileges(userName, dbName) {
            var url = 'setUserPrivileges/' +userName+ '/' + dbName;
            this.http(url).then(resp => {
                alert('Пользователь ' + userName + ': Права к базе ' +dbName+ ' установлены');
                let res = resp;
            });
        },

        // Удалить привилегии к базе
        delUserPrivileges(userName, dbName) {
            var url = 'delUserPrivileges/' +userName+ '/' + dbName;
            this.http(url).then(resp => {
                alert('Пользователь ' + userName + ': Права к базе ' +dbName+ ' удалены');
                let res = resp;
            });
        },

        // установить супер статус пользователя
        setUserSuperStatus(userName) {
            if(!userName) {
                alert('Не выбран пользователь');
                return false;
            }
            var url = 'setSuperUser/' + userName;
            this.http(url).then(resp => {
                alert('Пользователь ' + userName + ': Права суперпользователя установлены');
                let res = resp;
            });
        },

        // удалить права супер статус пользователя
        delUserSuperStatus(userName) {
            if(!userName) {
                alert('Не выбран пользователь');
                return false;
            }
            var url = 'delSuperUser/' + userName;
            this.http(url).then(resp => {
                alert('Пользователь ' + userName + ': Права суперпользователя удалены');
                let res = resp;
            });
        },

        //###################################
        // Функции для работы с базой данных
        showDatabaseList() {
            var url = 'SHOW_DATABASE_LIST/';
            this.http(url).then(resp => {
                this.databaseList = resp;
            });
        },

        getCurrentDatabase() {
            var url = 'CURRENT_DATABASE';
            this.http(url).then(resp => {
                this.currentDatabase = resp[0]['current_database'];
            });
        },

        // Создаем новый конфиг базы
        saveConfig() {
            var url = 'SAVE_CONFIG';
            var postData = this.dbConf;
            this.http(url, postData, 'post').then(resp => {
                this.getTableList();
            });
        },

        //
        getCurConfig() {
            var url = 'GET_CUR_CONFIG';
            this.http(url).then(resp => {
                this.curConfig = resp;
                this.dbConf = resp;
                this.dbConfPrint = Object.assign({}, resp);
            });
        },

        selectDbItem(item) {
           this.selectDbName = item.datname;
           this.dbItem = item;
        },

        addNewDb() {
            if(!this.newDbName) {
                alert('Нет имени базы');
                return false;
            }
            var url = 'addNewDb/' + this.newDbName;
            this.http(url).then(resp => {
                let r = resp;
                this.showDatabaseList();
            });
        },

        deleteDb(dbName) {
            let message = 'Не выбрана база';
            let name = this.checkName(dbName, 'selectDbName', message);
            if(!name) return false;

            var url = 'deleteDb/' + name;
            this.http(url).then(resp => {
                this.alertMessageOpen('Успешное удаление базы');
                this.showDatabaseList();
            });
        },

        // Удалить привилегии к базе
        getDbDump(userName, userPassword, dbName) {
            var remoteUrl = 'http://185.63.191.96';
            var apiUrl  = this.apiUrl;
            this.apiUrl = remoteUrl;
            var url = 'pg_dump.php?user=' +userName+ '&password=' + userPassword + '&dbname=' + dbName;
            this.http(url).then(resp => {
                this.apiUrl = apiUrl;
                this.remoteUrl = remoteUrl + '/' + resp;
                alert('Дамп базы выполнен');
                let res = resp;
            });
        },

    }  // Methods
};