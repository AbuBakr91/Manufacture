<template>
    <div class="modal-backdrop"></div>
    <div class="modal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="title-product text-center">
                    <h5><b>Наименование:</b></h5>
                    <h4>{{task}}</h4>
                </div>
                <div class="count-product text-center">
                    <h5><b>Готово:</b></h5>
                    <input type="number" v-model="count" class="form-control input_count">
                </div>
                <div class="count-product mt-3 text-center">
                    <h5><b>Брак:</b></h5>
                    <input type="number" v-model="defects" class="form-control input_count">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" @click="addCountTask" class="btn">Внести</button>
            </div>
        </div>
    </div>
</template>

<script>
import store from "../store"
export default {
    props:['task'],
    emits:['close'],
    data() {
        return {
            count: 10,
            defects: 0,
            user: JSON.parse(store.state.auth.user),
        }
    },
    methods: {
        async addCountTask() {
            await axios.post('/api/work-time/', {
                user_id: this.user.id,
                finish: true,
                count: this.count,
                defects: this.defects
            })
            this.$emit('close')
        }
    },
    mounted() {
        console.log(this.task)
    }
}
</script>

<style scoped>
.modal {
    width:650px;
    left: 30%;
}

.modal-backdrop {
    opacity: 0.5;
}

.btn {
    font-family: "Roboto", sans-serif;
    text-transform: uppercase;
    outline: 0;
    background: #376DA6;
    width: 100%;
    border: 0;
    height: 70px;
    padding: 8px;
    color: #FFFFFF;
    font-size: 14px;
    cursor: pointer;
    border-radius: 5px;
}

input {
    height: 50px;
}
</style>
