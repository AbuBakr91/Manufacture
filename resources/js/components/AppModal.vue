<template>
    <div class="modal-backdrop"></div>
    <app-loader v-if="loading"></app-loader>
    <div class="modal modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="m-auto">
                    <h5><b>Наименование:</b></h5>
                    <div class="title-product text-center">
                        <h4>{{task.name}}</h4>
                    </div>
                </div>
                <button type="button" class="close ml-0" @click="$emit('close')">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
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
import AppLoader from "../components/AppLoader"
export default {
    props:['task'],
    emits:['close'],
    data() {
        return {
            count: '',
            defects: 0,
            loading: false,
            user: JSON.parse(store.state.auth.user),
        }
    },
    methods: {
        async addCountTask() {
            this.loading = true
            await axios.post('/api/work-time/', {
                user_id: this.user.id,
                finish: true,
                count: this.outputCount(this.count),
                defects: this.defects
            })

            if (this.outputCount(this.count) !== 0) {
                const response = await this.spendOperation(false)

                if(!!response.moment && this.defects) {
                    const defects = await this.operationDefects(false)
                    const realShift = await this.operationRealShift(false)

                    if (!!defects.errors) {
                        await this.operationDefects(true)
                        axios.post('/api/logging', {
                            error: defects.errors,
                            card_id: this.task.name
                        })
                    }

                    if (!!realShift.errors) {
                        await this.operationRealShift(true)
                        axios.post('/api/logging', {
                            error: realShift.errors,
                            card_id: this.task.name
                        })
                    }

                    this.$emit('close', {
                        type: 'primary',
                        title: 'Успешно!',
                        text: `Операции в мойсклад созданы!`
                    })
                }

                if(!!response.errors) {
                    axios.post('/api/logging', {
                        error: response.errors[0].error,
                        card_id: this.task.name
                    })
                    const res = await this.spendOperation(true)
                    if (this.defects) {
                        await this.operationDefects(true)
                        await this.operationRealShift(true)
                    }

                    if (!!res.moment) {
                        this.$emit('close', {
                            type: 'primary',
                            title: 'Успешно!',
                            text: `Операции в мойсклад созданы!`
                        })
                    } else {
                        axios.post('/api/logging', {
                            error: res.errors,
                            card_id: this.task.name
                        })
                        this.$emit('close', {
                            type: 'danger',
                            title: 'Ошибка!',
                            text: `${res.errors}`
                        })
                    }
                }
            }
            if (this.outputCount(this.count) === 0 && this.defects){
                const defects = await this.operationDefects(false)
                const realShift = await this.operationRealShift(false)

                if (!!defects.errors) {
                    await this.operationDefects(true)
                    axios.post('/api/logging', {
                        error: defects.errors,
                        card_id: this.task.name
                    })
                }

                if (!!realShift.errors) {
                    await this.operationRealShift(true)
                    axios.post('/api/logging', {
                        error: realShift.errors,
                        card_id: this.task.name
                    })
                }

                this.$emit('close', {
                    type: 'primary',
                    title: 'Успешно!',
                    text: `Операции в мойсклад созданы!`
                })
            }
            this.loading = false
        },
        async spendOperation(applicable) {
            const data = await axios.post('/api/material/', {
                "card_id" : this.task.tech_id,
                "count" : this.outputCount(this.count),
                "office" : this.user.office,
                "defects" : this.defects,
                applicable
            })

            return data.data

        },
        async operationDefects(applicable) {
            await axios.post('/api/defects/', {
                "card_id" : this.task.tech_id,
                "defects" : this.defects,
                "office" : this.user.office,
                applicable
            })
        },
        async operationRealShift(applicable) {
            await axios.post('/api/retail-shift/', {
                "card_id" : this.task.tech_id,
                "defects" : this.defects,
                applicable
            })
        },
        outputCount(num) {
          if(num === '') {
            return parseInt(0)
          }
          return parseInt(num)
        }
    },
    components: {AppLoader}
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
