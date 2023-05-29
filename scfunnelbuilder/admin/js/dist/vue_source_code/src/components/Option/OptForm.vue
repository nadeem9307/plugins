<script setup>
import { ref, onUpdated, onMounted } from 'vue';
import BaseInput from '../inputs/BaseInput.vue';
import { useNodeStore } from '@/store/nodes';
import BaseButton from '../buttons/BaseButton.vue';
const nodeStore = useNodeStore();
import Multiselect from '@vueform/multiselect';
import BaseSelect from '../inputs/BaseSelect.vue';
import { useForm } from "vee-validate";

const { handleSubmit } = useForm();

const props = defineProps({
    nodeData: Object,
    required: true,
})

const updateNodeHandler = handleSubmit((values) => {
    const nodeId = props.nodeData?.step_node_id;
    const updateObj = {
        description: values.page,
        step_slug: values.pageSlug,
    }
    const updatedArray = nodeStore.getDataFlow.map((item) => {
        if (item.id === nodeId) {
            return {
                ...item,
                data: {
                    ...item.data,
                    ...updateObj
                },
            };
        }
        return item;
    });
    nodeStore.closeModal();
    nodeStore.updateDataFlow(updatedArray);
});

let page_name = ref(props.nodeData.description);
let page_slug = ref(props.nodeData.step_slug);
let product_name = ref(props.nodeData.step_id);

onMounted(() => {
    page_name.value = props.nodeData.description;
    page_slug.value = props.nodeData.step_slug;
    product_name.value = props.nodeData.step_id;
})
</script>
<template>
    <form @submit.prevent="updateNodeHandler" class="">
        <div class="mb-3 opt_label">
            <BaseInput v-model="page_name" :name="'page'" label-name="Page Name" />
        </div>
        <div v-if="nodeData.design_type !== 'product'" class="mb-3 opt_label">
            <BaseInput v-model="page_slug" :name="'pageSlug'" label-name="Page Slug" />
        </div>
        <div v-else class="mb-3 opt_label">
            <div class="select_prd_container">
                <label class="sub_title fw-500">Select Product</label>
                <BaseSelect name="'product'" :key="'default_2'" v-model="product_name"
                    :optionsData="nodeStore.getAllProductList" :placeholder="'Select product'" />
            </div>
        </div>
        <div class="">
            <BaseButton type="submit"> update Node {{ product_name }} </BaseButton>
        </div>
    </form>
</template>
<style lang="scss">
.select_prd_container {
    display: block;

    .fw-500 {
        font-weight: 500;
    }

    h4 {
        margin-bottom: 10px;
    }
}
</style>