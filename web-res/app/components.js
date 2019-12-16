Vue.component('modal-form', {
    props : ['modal_id', 'wrap_class'],
    data: function () {
        var wrapClass = 'col-sm-offset-2 col-xs-offset-0 col-md-8 col-sm-8';
        if(this.wrap_class)
            wrapClass = this.wrap_class;

        return {
            wrapClass,
            title: 'Модальный компонент',
            modalDesc : 'Lorem ipsum dolor sit',
        }
    },
    template: `
    
       <div class="modal fade subscribe padding-top-120 in" :id="modal_id" role="dialog"
             style="display: none; padding-right: 17px;">
            <div class="modal-dialog">
        
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="section-title margin-top-30">
                                    <button type="button" class="btn pull-right" data-dismiss="modal">
                                         <i class="fa fa-close"></i>
                                    </button>
                                    <!--<h2>{{title}}</h2>-->
                                    <slot name="title"></slot> 
                                    <div class="divider dark">
                                        <slot name="icon">
                                            <i class="icon-envelope-letter"></i>
                                        </slot>
                                    </div>
                                    <!--<p>{{modalDesc}}</p>-->
                                    <slot name="desc"></slot> 
                                </div>
                            </div>
                        </div>
        
                        <div class="row" style="" >
                            <div :class="wrapClass" >
                            <!--<div class="col-8" style="padding:2%;" >-->
                                <div class="margin-bottom-10" style="padding:2%;"  >
                                    <form id="mc-form" novalidate="true"> 
                                        <div class="subscribe-form" style="text-align:center">
                                            <slot name="content"></slot>  
                                            <!--<input id="mc-email" type="email" placeholder="Email Address" class="text-input"-->
                                                   <!--name="EMAIL">-->
                                            <!--<button class="submit-btn" type="submit">Submit</button>-->
                                        </div>
                                        <label for="mc-email" class="mc-label"></label>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    `,
});


Vue.component('current-info', {
    props : ['data_info'],
    data: function () {
        return { }
    },

    computed : {
        curUserInfo() {
            return this.data_info;
        }
    },

    template: `
    
       <div style="display: flex;" >
        
            <div class="table-responsive" style="width:25%" >
                <table class="table">
                    <tr>
                        <td style="border: 3px solid #ddd;" >Текущая база</td>
                        <td style="border: 3px solid #ddd;" >{{curUserInfo.datname}}</td>
                    </tr>
                </table>
            </div>
    
            <div class="table-responsive" style="width:25%" >
                <table class="table">
                    <tr>
                        <td style="width:200px; border: 3px solid #ddd;" >Текущий пользователь</td>
                        <td style="border: 3px solid #ddd;" >{{curUserInfo.usename}}</td>
                    </tr>
                </table>
            </div>
    
            <div class="table-responsive" style="width:25%" >
                <table class="table">
                    <tr>
                        <td style="border: 3px solid #ddd;" >Ip-адрес</td>
                        <td style="border: 3px solid #ddd;" >{{curUserInfo.client_addr}}</td>
                    </tr>
                </table>
            </div>
    
            <div class="table-responsive" style="width:25%" >
                <table class="table">
                    <tr>
                        <td style="border: 3px solid #ddd;" >Порт</td>
                        <td style="border: 3px solid #ddd;" >{{curUserInfo.client_port}}</td>
                    </tr>
                </table>
            </div>

        </div>
    `,
});


Vue.component('select-object-info', {
    props : ['info'],
    data: function () {
        return { }
    },

    computed : {
        curInfo() {
            return this.info;
        }
    },

    template: `
    
       <div style="display: flex;" >
        
            <table v-if="curInfo.db" class="table" style="width:33%" >
                <tr>
                    <td style="border: 2px solid #ddd; margin:0px !important;padding:5px !important; width:50%; text-align:right;"  
                       >Выбранная база</td>
                    <td style="border: 2px solid #ddd; margin:0px !important;padding:5px !important;" 
                       >{{curInfo.db}}</td>
                </tr>
            </table>
           
            <table v-if="curInfo.user" class="table" style="width:35%" >
                <tr>
                    <td style="border: 2px solid #ddd; margin:0px !important;padding:5px !important; width:50%; text-align:right;"  
                       >Выбранная user</td>
                    <td style="border: 2px solid #ddd; margin:0px !important;padding:5px !important;" 
                       >{{curInfo.user}}</td>
                </tr>
            </table>
 
            <table v-if="curInfo.table" class="table"  style="width:33%" >
                <tr>
                    <td style="border: 2px solid #ddd; margin:0px !important;padding:5px !important; width:50%; text-align:right;" 
                        >Выбранная таблица</td>
                        
                    <td style="border: 2px solid #ddd; margin:0px !important;padding:5px !important;" 
                        >{{curInfo.table}}</td>
                </tr>
            </table>
      
        </div>
    `,
});


//////////////////////////////////
Vue.component('sql-query-form', {
    props : ['table_name'],
    mixins: [Http, BaseMixin, dragMixin],
    data: function () {
        return { }
    },

    computed: {
        getTableName() {
            this.tableName = this.table_name;
            return this.tableName;
        }
    },

    methods: {

        queryExecute() {
            if(!this.sqlCommand) {
                alert('Пустая команда');
                return false;
            }
            this.execSqlCommand(null, this.emitResponse);
        },

        emitResponse(resp) {
           this.$emit('get_query_response', { result : resp });
        },

        // // Выполняем sql команды
        // execSqlCommand(paramName = '', callback = null) {
        //     var url = 'EXEC_SQL_COMMAND/' + this.sqlCommand + '/' + this.sqlCommandType;
        //     this.http(url).then(resp = > {
        //         if(paramName)this[paramName] = resp;
        //         if (callback) callback(resp);
        //     });
        // },

    },
    template: `
     <div> 
          <div class="col-sm-12" style="padding: 0px; display: flex; height:45px; z-index:99">
          
                <div style="display: none" >{{getTableName}}</div>
                
                <textarea 
                    v-model="sqlCommand" style="font-style: italic; width:70%; height: 100%;" 
                    type="text" class="form-control" placeholder="sql-запрос"
                ></textarea>
                
                <select v-model="sqlCommandType" class="form-control" 
                       style="margin:0px 5px 0px 5px;font-style: italic; width:20%; height: 100%" >
                       <option value="query" >Выборка (query)</option>
                       <option value="exec"  >Выполнение (exec)</option>
                       <option value="add_fields" >Добавить новые поля в таблицу</option>
                </select>
                
                <!--<input v-model="sqlCommandType" style="font-style: italic; width:20%; height: 34px;"-->
                       <!--type="text" class="form-control" placeholder="тип команды : query / exec">-->
                       
                <button @click="queryExecute()"
                        class="btn" style="width:20%; font-weight: bold;border-radius: 0px" >Выполнить sql-запрос</button>
          </div>
          
          <!--<div>{{freeSqlCommandResult}}  {{sqlCommandType}}</div> -->
     </div>
    `,
});



//////////////////////////////////
Vue.component('alert-message', {
    props : ['message', 'show'],
    data: function () {
        var alertMessage = this.message;
        if(!alertMessage) alertMessage = 'Успешное сохранение';
        return {
            alertMessage,
            showStatus : '',
        }
    },

    computed: {
        getStatus() {
            this.close();
            this.showStatus = this.show;
            return this.showStatus;
        }
    },

    created() {
        this.close();
    },

    methods: {
        close() {
            setTimeout(() => {
                 this.$emit('alert_message_close', true);
            }, 2000);
        },
    },

    template: ` 
          <div style="position: fixed; top:2%; border:0px red solid; width:50%; margin:0px auto 0px auto; z-index:999999" >
                 <div class="contact-us-detail"
                      style="color:white; text-align:center;  z-index:99999999; border:0px green solid; background: #1b9448; opacity: 0.7" >
                     {{alertMessage}}
                 </div>
          </div>
    `,
});

