<template>
    <div class="card mb-3 mt-5">
        <div class="card-header">
            <h4 class="mb-0 text-center">Профиль менеджера</h4>
        </div>
        <div class="card-body text-center">
            <img :src="url" alt="Christina Mason" class="img-fluid rounded-circle mb-2" width="128" height="128">
            <h5>{{user.firstname}} {{user.lastname}}</h5>
            <div class="text-muted mb-2">{{user.slug}}</div>

            <div>
                <form @submit="changePassword">
                    <div class="form-group form-change">
                        <label for="password" class="col-form-label">Введите новый пароль</label>
                        <input type="password" v-model="form.password" class="form-control" id="password">
                        <label for="password_confirm" class="col-form-label">Повторите пароль</label>
                        <input type="password" v-model="form.passwordConfirm" class="form-control" id="password_confirm">
                    </div>
                    <div class="btn_change mt-3">
                        <button type="button" class="btn btn-primary" :disabled="form.password != form.passwordConfirm || form.password.length < 6">Изменить</button>
                    </div>
                </form>
            </div>
        </div>
        <hr class="my-0">
    </div>
</template>

<script>
import store from "../../store";

export default {
    data() {
     return {
         form: {
             password:'',
             passwordConfirm: ''
         },
         user: JSON.parse(store.state.auth.user),
         url: '/img/avatar.png'
     }
    },
    methods: {
        changePassword() {

        }
    }
}
</script>

<style scoped>
.form-change {
    width: 600px;
    margin: auto;
}
</style>
