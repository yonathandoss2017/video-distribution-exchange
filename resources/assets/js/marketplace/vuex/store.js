import Vue from 'vue'
import Vuex from 'vuex'
Vue.use(Vuex)

let store = new Vuex.Store({
  state:{
    mediaTypeIndex:2,
    typeId:0,
    typeStyle:0,
    lanage:'',
    purchaseQuantity:null,
  },
  mutations:{
    newMediaTypeIndex (state, msg){
      state.mediaTypeIndex = msg
    },
    newTypeId (state, msg){
      state.typeId = msg
    },
    newTypeStyle (state, msg){
      state.typeStyle = msg
    },
    newLanage (state, msg){
      state.lanage = msg
    },
    newPurchaseQuantity(state, msg){
      state.purchaseQuantity = msg
    }
  }
})

export default store
