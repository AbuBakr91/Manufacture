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
        operationDefects(id, defects, applicable) {
            axios.post('/api/defects/', {
                "card_id" : id,
                "defects" : defects,
                applicable
            })
        },
       async spendOperation() {
            const data = await axios.post('/api/material/', {
                "card_id" : this.task.tech_id,
                "count" : this.task.count,
                "defects" : this.task.defects,
                "moment" : this.task.finish
            })

           console.log(data)

           if(data.data.errors) {
               this.$emit('danger', {
                   "code" : data.data.errors[0].code,
                   "message" : data.data.errors[0].error
               })

           const response = await axios.post('/api/material/', {
               "card_id" : this.task.tech_id,
               "count" : this.task.count,
               "defects" : this.task.defects,
               "moment" : this.task.finish,
               "applicable" : true
           })

           if (!!response.data.moment) {
               axios.post('/api/operation-status', {"id" : this.task.id})
               if (this.task.defects > 0) {
                   this.operationDefects(this.task.tech_id, this.task.defects, false)
               }
               this.$emit('success', this.task.tech_id)
            }
           } else {
               await axios.post('/api/operation-status', {"id" : this.task.id})
               if(this.task.defects > 0) {
                   await this.operationDefects(this.task.tech_id, this.task.defects, true)
               }
               this.$emit('success', this.task.tech_id)
           }


        }
    }
}
</script>

<style scoped>

</style>
