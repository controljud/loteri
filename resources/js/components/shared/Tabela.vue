<template>
    <div>
        <div class="row">
            <div class="col-md-12">
                <b-table responsive striped hover
                    :items="items"
                    :fields="fields"
                    :busy="isBusy"
                >
                    <template #cell(acertos)="item">
                        <b-form-rating v-if="item.value == 0" id="rating-6" v-model="item.value" stars="6" disabled  style="max-width: 175px; margin: 0 auto;"></b-form-rating>
                        <b-form-rating v-if="item.value > 0 && item.value <= 3" id="rating-6" v-model="item.value" stars="6" variant="warning" readonly style="max-width: 175px; margin: 0 auto;"></b-form-rating>
                        <b-form-rating v-if="item.value > 3 && item.value <= 5" id="rating-6" v-model="item.value" stars="6" variant="primary" readonly style="max-width: 175px; margin: 0 auto;"></b-form-rating>
                        <b-form-rating v-if="item.value > 5" id="rating-6" v-model="item.value" stars="6" variant="success" readonly style="max-width: 175px; margin: 0 auto;"></b-form-rating>
                    </template>

                    <template #cell(actions)="row">
                        <b-button-group>
                            <b-button size="sm" class="btn btn-sm btn-primary" @click="edit(row.item)">
                                <font-awesome-icon icon="fa-solid fa-edit"/>
                            </b-button>
                            
                            <b-button size="sm" class="btn btn-sm btn-danger" @click="doDelete(row.item)">
                                <font-awesome-icon icon="fa-solid fa-trash" />
                            </b-button>
                        </b-button-group>
                    </template>

                    <template #table-busy>
                        <div class="text-center text-success my-2">
                            <b-spinner class="align-middle"></b-spinner>
                            <strong>Carregando...</strong>
                        </div>
                    </template>
                </b-table>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <paginate
                    :page-count="last_page"
                    :click-handler="linkGen"
                    :prev-text="'<<'"
                    :next-text="'>>'"
                    :container-class="'pagination'"
                ></paginate>
            </div>
        </div>
    </div>
</template>

<script>
import Paginate from 'vuejs-paginate';

export default ({
    components: {
        'paginate': Paginate
    },

    props: [
        'idTabela', 'items', 'fields', 'last_page', 'isBusy'
    ],

    data() {
        return {

        }
    },

    created: function() {

    },

    methods: {
        edit(item) {
            this.$emit('edit', item);
        },

        doDelete(item) {
            this.$emit('doDelete', item);
        },

        linkGen(page) {
            this.$emit('linkGen', page)
        }
    }
})
</script>

<style>
.pagination {
	list-style: none;
	display: flex;
}

.pagination li {
	width: 30px;
	height: 30px;
	display: flex;
	justify-content: center;
	align-items: center;
	border: 1px solid #D4E6F1;
	cursor: pointer;
}

.pagination li:hover {
	background-color: #D4E6F1;
}

.pagination li:active {
	background-color: #2980B9;
}

.active {
	background-color: #2980B9;
}

.pagination li a {
	text-decoration: none;
	display: block;
	color: #2980B9;
}

.pagination li.active a {
	color: white;
}
</style>