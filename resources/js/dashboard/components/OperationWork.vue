<template>
    <tr>
        <td>{{task.firstname}} {{task.lastname}}</td>
        <td>{{task.name}}</td>
        <td>{{task.count}}</td>
        <td>{{task.defects}}</td>
        <td>{{task.finish}}</td>
        <td><button class="btn btn-primary" @click="spendOperation">провести</button></td>
    </tr>
</template>

<script>
export default {
    props: ['task'],
    emits: ['success'],
    methods: {
       async spendOperation() {
            const data = await axios.post('/api/material/', {
                "card_id" : this.task.tech_id,
                "count" : this.task.count,
                "defects" : this.task.defects,
                "moment" : this.task.finish
            })

           if(!!data.data.moment) {
               axios.post('/api/operation-status', {"id" : this.task.id})
               this.$emit('success', this.task.tech_id)
           }

           if(data.data.errors[0].code) {
               this.$emit('danger', data.data.errors[0].code)

               const data = await axios.post('/api/material/', {
                   "card_id" : this.task.tech_id,
                   "count" : this.task.count,
                   "defects" : this.task.defects,
                   "moment" : this.task.finish,
                   "applicable" : true
               })
           }

        }
    }
}
</script>

<style scoped>

</style>
