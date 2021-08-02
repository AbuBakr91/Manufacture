<template>
    <tr @click="show = !show" class="card_row">
        <td>{{i+1}}</td>
        <td>{{card.name}}</td>
        <td contenteditable @blur="saveDynamic($event, card.id)">
            {{((card.dynamic_time)/60).toFixed(1)}}
        </td>
        <td contenteditable @blur="saveStatic($event, card.id)">
            {{(card.statistical_time ?? 0).toFixed(1)}}
        </td>
        <td>{{getDate(card.updated_at)}}</td>
        <td><button @click="updateCard(card.id, card.name)" class="btn btn-primary">Обновить</button></td>
    </tr>
    <tr>
        <td colspan="5">
            <ol v-if="show">
                <li v-for="materil in card.materials">
                    {{materil.name}} - {{materil.count}} шт.
                </li>
            </ol>
        </td>
    </tr>
</template>

<script>
import axios from "axios";

export default {
    props : ['card', 'i'],
    emits: ['update'],
    data() {
        return {
            show: false,

        }
    },
    methods: {
        getDate(data) {
            return data.slice(0, 10)
        },
        async saveStatic(event,id) {
            await axios.post('/api/record-time', {
                id : id,
                time : event.path[0].innerText,
                static: true
            })
        },
        async saveDynamic(event,id) {
            await axios.post('/api/record-time', {
                id : id,
                time : event.path[0].innerText,
                dynamic: true
            })
        },
        updateCard(id, name) {
            this.$emit('update', {id, name})
        }
    }
}
</script>

<style scoped>
.card_row {
    cursor: pointer;
}
</style>
