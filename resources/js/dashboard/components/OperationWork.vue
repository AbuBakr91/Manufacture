<template>
    <tr>
        <td>{{task.firstname}} {{task.lastname}}</td>
        <td>{{task.name}}</td>
        <td>{{task.count}}</td>
        <td>{{task.defects}}</td>
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
                "defects" : this.task.defects
            })
           console.log(!!data.data.name)
           console.log(data.data)
           if(!!data.data.name) {
               axios.post('/api/operation-status', {"id" : this.task.id})
               this.$emit('success', this.task.tech_id)
           }

           if(data.data.errors[0].code === 3007) {
                alert('Нельзя использовать отсутствующий на складе товар')
           }

        }
    }
}
</script>

<style scoped>

</style>
