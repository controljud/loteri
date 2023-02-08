<template>
    <div>
        <b-modal id="confirmaModal" hide-footer hide-header-close>
            <template #modal-title>Exclusão</template>
            <div class="d-block text-left">
                <div class="row">
                    <div class="col-md-12">
                        <p>Tem certeza que deseja fazer a exclusão desse ítem?</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        {{ texto }}
                    </div>
                </div>

                <hr />

                <div class="row">
                    <div class="col-md-12 right">
                        <b-button type="button" variant="success" block @click="excluir(); $bvModal.hide('confirmaModal');">
                            <font-awesome-icon icon="fa-solid fa-trash" />
                        </b-button>

                        <b-button type="button" variant="light" block >
                            <font-awesome-icon icon="fa-solid fa-close" @click="$bvModal.hide('confirmaModal');"/>
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
                texto
            }
        },

        methods: {
            excluir() {
                this.$emit('delete', true);
            },

            selectItem (item) {
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