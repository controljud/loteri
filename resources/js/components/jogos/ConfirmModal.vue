<template>
    <div>
        <b-modal id="confirmModal" hide-footer hide-header-close>
            <template #modal-title>Exclusão de aposta</template>
            <div class="d-block text-left">
                <div class="row">
                    <div class="col-md-12">
                        <p>Tem certeza que deseja excluir essa aposta?</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <p>{{ item.numero }} - {{ item.apostado }} <br />
                            {{ item.descricao }}</p>
                        <p v-if="item.acertos == 1">{{ item.acertos }} acerto</p>
                        <p v-if="item.acertos != 1">{{ item.acertos }} acertos</p>
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-12 right">
                        <b-button type="button" variant="success" block @click="excluirAposta(); $bvModal.hide('confirmModal');">
                            <font-awesome-icon icon="fa-solid fa-trash" />
                        </b-button>

                        <b-button type="button" variant="light" block >
                            <font-awesome-icon icon="fa-solid fa-close" @click="$bvModal.hide('confirmModal');"/>
                        </b-button>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                item: {
                    numero: null,
                    apostado: null,
                    descricao: null,
                    acertos: 0
                }
            }
        },

        methods: {
            excluirAposta() {
                this.$emit('deleteAposta', this.item);
            },

            selectItem(item) {
                this.item = item;
            }
        }
    }
</script>

<style scoped>
.right {
    text-align: right;
}
</style>