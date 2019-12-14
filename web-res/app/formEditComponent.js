
Vue.component('form-edit-com', {
    props : ['data', 'form_model', 'select_items'],
    data: function () {
        return {
            form : [],
            item : {},

            inpClass : 'form-control',
            emitActionName : 'get_form_data',
            selId : 'id',
            selName : 'name',
            rowName : 'row',
            endName : 'end',
        }
    },

    computed : {

        getItem() {
            this.item = this.data;
            return this.item;
        },

        getModel() {
            var model = this.form_model;
            this.form = this.formDataConvert(model);
            return this.form;
        },

    },


    methods: {

        emitValues(fname) {
            let item = this.item;
            var resp = { item, fname };
            var actionName = this.emitActionName;
            this.$emit(actionName, resp);
        },

        formDataConvert(model) {

            var obj  = {};
            var result = [];
            var rowStart = false;

            for(var fname in  model) {
                var item = model[fname];
                switch(item.pos) {
                    case this.rowName : rowStart = true ; break;
                    case this.endName : rowStart = false; break;
                }

                if(rowStart) {
                    obj[fname] = item;
                } else {
                    obj[fname] = item;
                    result.push(obj);
                    obj = {};
                }
            }

            return result;
        },

        // $emit('input', $event.target.value)
    },

    template: `
       <div class="form-model-container" >
            
            <!--<pre style="width:50%;" >{{getItem}}</pre>-->
            <!--<pre style="width:50%;" >{{getModel}}</pre>-->
   
            <template v-for="(row, i) in getModel" >
               <div class="form-model-row" style="display: flex; background: white; border:0px red solid;" >
                   <template v-for="(formItem, fname) in row" > 
                   
                       <div class="form-model-item" 
                           :style="'padding:5px; background: gainsboro; border:0px green solid; width:' + formItem.width" >
                       
                              <label class="_form-label" >{{formItem.title}}</label>
                              
                              <template v-if="formItem.type == 'text'" >
                              
                                  <input v-model="getItem[fname]" @input="emitValues(fname)" 
                                         type="text" :class="inpClass + ' _inputText'" > 
                                         
                              </template>
                              <template v-else-if="formItem.type == 'hidden'" >
                              
                                  <input v-model="getItem[fname]" @input="emitValues(fname)" 
                                         type="hidden" :class="inpClass + ' _inputText'"> 
                                         
                              </template>
                              <template v-else-if="formItem.type == 'num'" >
                              
                                  <input v-model="getItem[fname]" @input="emitValues(fname)" 
                                         type="num" :class="inpClass + ' _inputText'" > 
                                         
                              </template>
                              <template v-else-if="formItem.type == 'checkbox'" >
                              
                                   <md-checkbox v-model="getItem[fname]" 
                                                @input="emitValues(fname)" 
                                                class="md-primary" 
                                                style="width:100%; display: block; margin:0px; padding:0px; border:1px red solid" 
                                   ></md-checkbox>
                                   
                                   <!--<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" :for="'my_custom_checkbox-' + fname">-->
                                        <!--<input v-model="getItem[fname]" @input="emitValues(fname)"  :id="'my_custom_checkbox-' + fname"-->
                                               <!--type="checkbox"  class="mdl-checkbox__input" >-->
                                        <!--<span class="mdl-checkbox__label">Супер пользователь</span>-->
                                   <!--</label>-->
                             
                                   <!--<input v-model="getItem[fname]" @input="emitValues(fname)" -->
                                         <!--type="checkbox" :class="inpClass + ' _inputCheckbox'" > -->
                                         
                              </template>
                              <template v-else-if="formItem.type == 'textarea'" >
                             
                                    <textarea  :class="inpClass + ' _inputTextarea'"
                                       v-model="getItem[fname]" @input="emitValues(fname)"  
                                    ></textarea>
                                  
                              </template>
                              <template v-else-if="formItem.type == 'select'" >
                     
                                    <select v-model="getItem[fname]" :class="inpClass + ' _inputSelect'" >
                                        <option v-for="(select) in select_items[fname]" 
                                                :value="select[selId]">{{select[selName]}}</option>
                                    </select>
                                  
                              </template>
                
                       </div><!-- form-model-item -->
          
                   </template>
               </div><!-- form-model-row -->
            </template>
 
       </div><!-- form-model-container -->
    `,
});

// Material Desing Lite
// https://getmdl.io/components/index.html#toggles-section
// <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
// <link rel="stylesheet" href="https://code.getmdl.io/1.3.0/material.indigo-pink.min.css">
// <script defer src="https://code.getmdl.io/1.3.0/material.min.js"></script>

// ################################
// Пример использования


// ----- формирование данных
// var itemTest = {
//     'user'     : 'w1user',
//     'password' : '1234567',
//     'dbname'   : 'maikldb',
//     'status'   : true,
//     'comment'  : '',
// };
//
// var itemTestModel = {
//     'user'    : { type : 'select',    pos : 'row', title : 'Пользователь', width: '35%', value : ''},
//     'password': { type : 'text',      pos : '', title : 'Пароль',       width: '35%', value : ''},
//     'status'  : { type : 'checkbox',  pos : 'end', title : 'Статус',       width: '30%', value : ''},
//     'comment' : { type : 'textarea',  pos : '', title : 'Комментарий',  width: '100%', value : ''},
//     'dbname'  : { type : 'select',    pos : '',    title : 'Выбрать базу', width: '100%', value : ''},
// };
//
// var selectTestDb = [
//     { id : 'maikldb',    name : 'maikldb', },
//     { id : 'reestrsrv',  name : 'reestrsrv', },
//     { id : 'reestrdb',   name : 'reestrdb', },
// ];
//
// var selectTestUser = [
//     { id : 'w1user',      name : 'w1user', },
//     { id : 'maikl_super', name : 'maikl_super', },
//     { id : 'test_user',   name : 'test_user', },
// ];


// -- подключение компоненты
// <form-edit-com
//     :data="itemTest"
//     :form_model="itemTestModel"
//     :select_items="{'dbname' : selectTestDb, 'user' : selectTestUser}"
//      @get_form_data="getFormDataTest"
// ></form-edit-com>

