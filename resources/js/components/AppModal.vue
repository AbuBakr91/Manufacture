<template>
    <div class="modal-backdrop" @click="$emit('close')"></div>
    <div class="modal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="title-product text-center">
                    <h5><b>Наименование:</b></h5>
                    <p>Relay-LED-auto после автоматического монтажа</p>
                </div>
                <div class="count-product text-center">
                    <h5><b>Введите количество:</b></h5>
                    <input type="number" value="1" class="form-control input_count">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" @click="$emit('close')" class="btn">Закрыть смену</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
emits:['close']
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
    padding: 8px;
    color: #FFFFFF;
    font-size: 14px;
    cursor: pointer;
    border-radius: 5px;
}
</style>
