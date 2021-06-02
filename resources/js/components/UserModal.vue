<template>
  <div class="modal-backdrop " @click="$emit('close')"></div>
  <div class="modal d-block" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4>Введите данные пользователя</h4>
          <button type="button" class="close" @click="$emit('close')" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <form @submit.prevent="addUser">
            <div class="form-group">
              <label for="firstname" class="col-form-label">Имя</label>
              <input type="text" v-model="form.firstname" class="form-control" id="firstname">
              <label for="lastname" class="col-form-label">Фамилия</label>
              <input type="text" v-model="form.lastname" class="form-control" id="lastname">
            </div>
            <div class="form-group">
              <label for="select-slug" class="col-form-label">Должность</label>
              <select class="form-control" name="select-slug" id="select-slug" v-model="form.slug">
                <option value="collector">Сборщик</option>
                <option value="machine-operator">Оператор станка</option>
                <option value="shareholder">Пайщик</option>
              </select>
            </div>
            <div class="form-group">
              <div class="form-group">
                <label for="email" class="col-form-label">Email</label>
                <input type="email" v-model="form.email"  class="form-control" id="email">
                <label for="password" class="col-form-label">Password</label>
                <input type="password" v-model="form.password" class="form-control" id="password">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')" data-dismiss="modal">Закрыть</button>
          <button type="button" class="btn btn-primary" @click="addUser">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      form: {
          firstname: '',
          lastname: '',
          slug: 'collector',
          email: '',
          password: ''
      }
    }
  },
    methods: {
     async addUser() {
         const data = await axios.post('/api/users', this.form)
         if(data.status === 200) {
             this.$emit('user', data.data.user)
         } else {
             this.$emit('warning')
         }
      }
    }
}
</script>

<style scoped>
.modal-backdrop {
  opacity: 0.5;
}
</style>
