<script setup>
import BaseSelect from '@/components/inputs/BaseSelect.vue';
import PageTemplate from './PageTemplate.vue';
import Multiselect from '@vueform/multiselect';
import { useNodeStore } from '@/store/nodes';
import { checkForStepType, getLastNode } from '@/utils/Common'
import BaseInput from '@/components/inputs/BaseInput.vue';
import Loader from '@/components/loader/Loader.vue';
import { useForm } from 'vee-validate';
import { ref, reactive, onMounted } from 'vue';
import { allPricePlans } from '@/api/funnel/funnel-service';

const nodeStore = useNodeStore();
const { handleSubmit } = useForm();
const pageTemplateRef = ref(null);
let pricePlans = ref([]);
let price = ref(null);
let productId = ref(null);
let formData = ref(null);

nodeStore.updateStepsTemplateData([]);
const handleImport = handleSubmit(async (values) => {
    // productId.value = values.product;
    formData.value = values;
});

onMounted(() => {
})

const handleImportSave = async (index, data) => {
    await handleImport();
    const { id: nodeId } = getLastNode();
    if (!formData.value) return
    pageTemplateRef.value[index].templateHandler(data, formData.value, nodeId)
}


const selectedProduct = async (id) => {
    pricePlans.value = null;
    price.value = null;
    const { response, error } = await allPricePlans(id);
    if (!response) return;
    pricePlans.value = response;
}

// select pricing plan
function handleSelectPlan(selected) {
    const selectedOption = pricePlans.value?.find(item => item.value === selected)
    price.value = selectedOption?.price ? selectedOption.price : null;
}


const deselectProductHandler = () => {
    pricePlans.value = null;
    price.value = null;
}
const deselectPlanHandler = () => {
    price.value = null;
}


</script>

<template>
    <div class="">
        <form class="mb-4">
            <div v-if="checkForStepType(nodeStore.getActiveStep.slug_type, ['landing', 'thankyou'], true)"
                class="base_select_box ">
                <BaseSelect @unselect="deselectProductHandler" @on-select="selectedProduct" :name="'product'"
                    :key="'default_11'" :optionsData="nodeStore.getAllProductList" label="Select Product"
                    :placeholder="'Select Product'" />
            </div>
            <template v-if="checkForStepType(nodeStore.getActiveStep.slug_type, ['upsell', 'downsell'])">
                <div class="base_select_box ">
                    <BaseSelect :name="'pricePlan'" :key="'default_22'" @unselect="deselectPlanHandler"
                        @on-select="handleSelectPlan" :optionsData="pricePlans" label="Select Pricing"
                        :placeholder="'Select Price'" />
                </div>
                <div class="price_inp_section">
                    <label class="sub_title pe-3">price</label>
                    <div class="custom_input ">
                        <input type="text" class="base_input form-control" :value="price" readonly>
                        <span class="price_symbol">$</span>
                    </div>
                </div>
            </template>
        </form>
        <div class="template_grid">
            <div v-if="!nodeStore.getStepsTemplateData.length" class="dot_loader_container">
                <Loader />
            </div>
            <div v-for="(templateData, index) in  nodeStore.getStepsTemplateData" :key="index" class="grid_item">
                <PageTemplate @save="handleImportSave(index, templateData)" ref="pageTemplateRef"
                    :templateData="templateData" />
            </div>

        </div>
    </div>
</template>

<style lang="scss">
.template_grid {
    display: flex;
    width: 100%;
    flex-direction: row;
    flex-wrap: wrap;
    gap: 10px 10px;
    height: 100%;
    position: relative;
    min-height: 250px;

    .grid_item {
        flex: 0 0 32%;
    }
}

.base_select_box {
    margin-bottom: 26px;
}

.dot_loader_container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9;
}
</style>