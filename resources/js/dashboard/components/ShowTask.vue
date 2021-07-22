<template>
    <tr @click="show = !show">
        <td>{{i+1}}</td>
        <td>{{card.name}}</td>
        <td>{{((card.dynamic_time)/60).toFixed(1)}}</td>
        <td contenteditable @blur="saveTime($event, card.id)" >
            {{((card.statistical_time)/60).toFixed(1)}}
        </td>
        <td>{{getDate(card.updated_at)}}</td>
    </tr>
    <tr>
        <td colspan="5">
            <ol v-if="show">
                <li v-for="materil in card.materials">
                    {{materil.material_name}} - {{materil.count}} шт.
                </li>
            </ol>
        </td>
    </tr>
</template>

<script>
export default {
    props : ['card', 'i'],
    data() {
        return {
            show: false,
        }
    },
    methods: {
        getDate(data) {
            return data.slice(0, 10)
        }
    }
}
</script>

<style scoped>

</style>
