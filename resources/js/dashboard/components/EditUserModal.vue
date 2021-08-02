<template>
  <div class="modal-backdrop " @click="$emit('close')"></div>
  <div class="modal d-block" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Введите данные для изменения</h4>
          <button type="button" class="close" @click="$emit('close')" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent>
            <div class="form-group">
              <label for="firstname" class="col-form-label">Имя</label>
              <input type="text" v-model="user.firstname" class="form-control" id="firstname">
              <label for="lastname" class="col-form-label">Фамилия</label>
              <input type="text" v-model="user.lastname" class="form-control" id="lastname">
            </div>
            <div class="form-group">
              <label for="select-slug" class="col-form-label">Отдел</label>
              <select class="form-control" name="select-slug" id="select-slug" v-model="user.deportment">
                  <option v-for="item in position" :value="item.id">{{item.name}}</option>
              </select>
            </div>
              <div class="form-group">
                  <label for="select-office" class="col-form-label">Офис</label>
                  <select class="form-control" name="select-office" id="select-office" v-model="user.office">
                      <option v-for="item in office" :value="item.id">{{item.name}}</option>
                  </select>
              </div>
            <div class="form-group">
              <div class="form-group">
                <label for="email" class="col-form-label">Email</label>
                <input type="email" v-model="user.email"  class="form-control" id="email">
                <label for="password" class="col-form-label">Password</label>
                <input type="password" v-model="user.password" class="form-control" id="password">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')" data-dismiss="modal">Закрыть</button>
          <button type="button" class="btn btn-primary" @click="editUser">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import store from '../../store'
import { mapGetters } from 'vuex'
import axios from "axios";
export default {
    data() {
        return {
            office: [],
            position: []
        }
    },
    methods: {
        async getPosition() {
            const data = await axios.get('/api/departments')
            this.position.push(...data.data)
        },
        async getWorkRoom() {
            const data = await axios.get('/api/work-rooms')
            this.office.push(...data.data)
        },
        async editUser() {
            await axios.patch('/api/users/' + this.user.id, this.user)
            this.$store.dispatch('clearEditUser')
            this.$emit('success')
        }
    },
    mounted() {
        this.getPosition()
        this.getWorkRoom()
        console.log(this.user)
    },
    computed: {
        ...mapGetters({
            user:  Object.freeze('getEditUser')
        })
    }
}
</script>

<style scoped>
.modal-backdrop {
  opacity: 0.5;
}
</style>
