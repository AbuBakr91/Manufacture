<template>
    <div class="alert" :class="alert.type">
        <h3>{{alert.title}}</h3>
        <p>{{alert.text}}</p>
        <button class="btn" @click="$emit('close')" :class="alert.type">Закрыть</button>
    </div>
</template>

<script>
export default {
    emits: ['close'],
    props: ['alert']
}
</script>

<style scoped>

</style>
