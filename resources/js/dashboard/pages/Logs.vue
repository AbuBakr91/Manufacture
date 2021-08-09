<template>
    <h4 class="text-center mt-3 ">Ошибки при проведении операций в мойсклад</h4>
    <table class="mt-3 table table-hover" v-if="logs.length !== 0">
        <thead>
        <tr>
            <th>#</th>
            <th>Текст ошибки</th>
            <th>Дата</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="(log,i) in logs">
                <td>{{i+1}}</td>
                <td>{{log.log_errors}}</td>
                <td>{{log.created_at.slice(0,10)}}</td>
            </tr>
        </tbody>
    </table>
    <div class="box_clear" v-if="logs.length !== 0">
        <button class="btn btn-primary btn__clear" @click="clear">Очистить</button>
    </div>
    <div v-else class="mt-5">
        <h4 class="text-center">Список пока пуст!</h4>
    </div>
</template>

<script>
export default {
    data() {
        return {
            logs: []
        }
    },
    methods: {
       async getLogs() {
            const data = await axios.get('/api/logging')
            this.logs.push(...data.data)
        },
        clear() {
           axios.delete('/api/logging/' + 1)
            this.logs = []
        }
    },
    mounted() {
        this.getLogs()
    },
    name: "Logs"
}
</script>

<style scoped>

.box_clear {
    width: auto;
}

.btn__clear {
    float: right;
    margin-right: 15px;
}
</style>
