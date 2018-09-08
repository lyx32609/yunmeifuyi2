//注册
Vue.component('type-petition',{
    template:'<td>{{message}}</td>',
    props:["message"],
    data () {
        return {
            messageState: this.message,   //就初始化的时候更新一次，但在模板里会动态更新
            typePetitionArr:['通用','领用','用车','付款','报销','采购','用证','用印','出差','加班','外出','转正','离职','请假','招聘'],
        }
    },
    watch:{
        message(val,oldVal){
            //console.log(val,oldVal);
          this.typePetitionEvent();
        }
    },
    methods:{
        typePetitionEvent(){
            for(var i=0;i<this.typePetitionArr.length;i++){
                if(this.message==i){
                    //console.log(this.typePetitionArr[i]);
                    this.message=this.typePetitionArr[i];
                }
            }
        }
    }
});


// 注册
// Vue.component('my-component',{
//     template:"<template><template v-for='item in list_add'><tr><td  class='tg-s6z2' rowspan='2' colspan='3'>审批人（加签）</td><td class='tg-s6z2' colspan='3'>部门</td><td class='tg-s6z2' colspan='6'>{{item.domain}}</td><td class='tg-s6z2' colspan='3'>姓名</td><td class='tg-s6z2' colspan='6'>{{item.name}}</td><td class='tg-s6z2' colspan='3'>日期</td><td class='tg-s6z2' colspan='6'>{{item.add_time}}</td></tr><tr><td class='tg-031e' colspan='27'><i>加签意见：</i><span>{{item.add_advice}}</span></td></tr></template></template>",
//     data(){
//         return{
//             list_add:[],
//         }
//     },
//     created(){
//         this.arrData();
//     },
//     methods:{
//         arrData(){
//             var ctx=this;
//             axios.post('get-date')
//                 .then(function(res){
//                     console.log(res);
//                     ctx.list_add=res.data.msg.list1;
//                     console.log(ctx.list_add);
//                 })
//                 .catch(function(err){
//                     console.log(err);
//                 })
//         }
//     }
// });
