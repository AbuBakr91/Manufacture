<template>
    <div class="message" v-if="message" :class="['alert', message.type]">
        <p class="alert-title" v-if="title">{{title}}</p>
        <p>{{message.value}}</p>
    </div>
</template>

<script>
import {computed} from 'vue'
import {useStore} from 'vuex'

export default {
    setup() {
        const store = useStore()
        const TITLE_MAP = {
            primary: 'Успешно!',
            danger: 'Ошибка!',
            warning: 'Внимание!'
        }

        const message = computed(() => store.state.message)
        const title = computed(() => message.value ? TITLE_MAP[message.value.type] : null)

        return {
            message,
            title
        }
    }
}
</script>

<style scoped>
.message {
    width: 800px;
    margin: auto;
}
</style>
