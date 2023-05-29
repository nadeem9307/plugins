<script setup>
import Multiselect from "@vueform/multiselect";
import { useNodeStore } from "@/store/nodes";
import BaseButton from "@/components/buttons/BaseButton.vue";
import BaseSelect from "@/components/inputs/BaseSelect.vue";
import { useForm } from "vee-validate";
import { onMounted, ref } from "vue";
import {
  addNewNode,
  allPricePlans,
  saveFunnel,
} from "@/api/funnel/funnel-service";
import { getLastNode, checkForStepType } from "@/utils/Common";
let pricePlans = ref([]);

const nodeStore = useNodeStore();
const { handleSubmit } = useForm();
let saveBtnEle = ref(null);

onMounted(() => {});

const createNodeHandler = handleSubmit(async (values) => {
  const latestNode = getLastNode();
  const newNode = {
    pageId: values?.pageId ? values?.pageId : "",
    step_product_id: values?.product ? values?.product : "",
    product_pricing_plan: values?.pricePlan ? values?.pricePlan : "",
    design_type: "use_existing",
    funnel_id: nodeStore.getFunnelId,
    step_node_id: latestNode?.id,
    slug_type: nodeStore.getActiveStep.slug_type,
  };
  if (saveBtnEle.value) {
    saveBtnEle.value.buttonRef.disabled = true;
    saveBtnEle.value.buttonRef.setAttribute("loader-indicator", "on");
  }
  const { error } = await addNewNode(newNode);
  saveBtnEle.value.buttonRef.disabled = false;
  saveBtnEle.value.buttonRef.removeAttribute("loader-indicator");
  if (error) return;
  nodeStore.closeModal();

  //   await saveFunnel();
});

const selectedProduct = async (id) => {
  pricePlans.value = null;
  const { response, error } = await allPricePlans(id);
  if (!response) return;
  pricePlans.value = response;
};

const deselectProductHandler = () => {
  pricePlans.value = null;
};
</script>
<template>
  <form @submit.prevent="createNodeHandler">
    <div class="mb-3">
      <div class="">
        <div class="base_select_box">
          <BaseSelect
            :name="'pageId'"
            :key="'default_2'"
            :optionsData="nodeStore.getAllPageList"
            label="Select Page"
            :placeholder="'Select page'"
          />
        </div>
        <template
          v-if="
            checkForStepType(nodeStore.getActiveStep.slug_type, [
              'upsell',
              'downsell',
            ])
          "
        >
          <div class="base_select_box">
            <BaseSelect
              @unselect="deselectProductHandler"
              @on-select="selectedProduct"
              :name="'product'"
              :key="'default_11'"
              :optionsData="nodeStore.getAllProductList"
              label="Select Product"
              :placeholder="'Select Product'"
            />
          </div>
          <div class="base_select_box">
            <BaseSelect
              :name="'pricePlan'"
              :key="'default_22'"
              :optionsData="pricePlans"
              label="Select Pricing"
              :placeholder="'Select Price'"
            />
          </div>
        </template>
        <div class="">
          <BaseButton ref="saveBtnEle" class="save_node" type="submit">
            <span
              class="spinner-border spinner_c spinner-border-sm"
              role="status"
              aria-hidden="true"
            ></span>
            <span class="save__">Save Node</span>
          </BaseButton>
        </div>
      </div>
    </div>
  </form>
</template>
<style lang="scss">
.save_node {
  min-width: 133px;
  text-align: center;

  .spinner_c {
    margin: 0 auto;
  }
}
</style>
