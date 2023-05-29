<script setup>
import BaseButton from "../buttons/BaseButton.vue";
import BaseInput from "../inputs/BaseInput.vue";
import Sidebar from "../sidebar/Sidebar.vue";
import SidebarHeader from "../sidebar/SidebarHeader.vue";
import { useForm } from "vee-validate";
import { ref, onMounted, onUpdated } from "vue";
import Multiselect from "@vueform/multiselect";
import { useNodeStore } from "@/store/nodes";
import { allPricePlans, updateStepNode } from "@/api/funnel/funnel-service";
import { checkForStepType } from "@/utils/Common";
const { handleSubmit } = useForm();
const nodeStore = useNodeStore();
let saveBtnEle = ref(null);
let product_name = ref(nodeStore.getNodeClickedData.step_product_id);
let price_plan = ref(nodeStore.getNodeClickedData.product_pricing_plan);
let pricePlans = ref([]);

let errorMessage = ref("");
let errorPriceMessage = ref("");
const checkNodeSlugType = (node_slug) => {
  const slugArray = ["percentage_split", "conditional_split"];
  return slugArray.includes(node_slug) ? false : true;
};
onUpdated(async () => {
  product_name.value = nodeStore.getNodeClickedData.step_product_id;
  price_plan.value = nodeStore.getNodeClickedData?.product_pricing_plan;
  if (!product_name.value) return;
  const { response, error } = await allPricePlans(product_name.value);
  if (!response) return;
  pricePlans.value = response;
});
onMounted(async () => {
  product_name.value = nodeStore.getNodeClickedData.step_product_id;
  price_plan.value = nodeStore.getNodeClickedData?.product_pricing_plan;
  if (!product_name.value) return;
  const { response, error } = await allPricePlans(product_name.value);
  if (!response) return;
  pricePlans.value = response;
});

const updateNodeHandler = handleSubmit(async (values) => {
  if (
    checkForStepType(nodeStore.getNodeClickedData.design_type, ["product"]) &&
    !product_name.value
  ) {
    errorMessage.value = "product is required";
    return;
  }
  if (
    (checkForStepType(nodeStore.getNodeClickedData.design_type, [
      "product_page_price",
    ]) &&
      !price_plan.value) ||
    !product_name.value
  ) {
    if (!price_plan.value) {
      errorPriceMessage.value = "Price Plan is required";
      return;
    }
    if (!product_name.value) {
      errorMessage.value = "product is required";
      return;
    }
  }
  const isValid = !values || errorMessage.value || errorPriceMessage.value;
  if (
    nodeStore.getNodeClickedData.design_type === "product_page_price" &&
    isValid
  )
    return;

  if (saveBtnEle.value) {
    saveBtnEle.value.buttonRef.disabled = true;
    saveBtnEle.value.buttonRef.setAttribute("loader-indicator", "on");
  }
  const nodeId = nodeStore.getNodeClickedData?.step_node_id;
  const pageData = {
    step_title: values?.page ? values?.page : "",
    step_slug: values?.pageSlug ? values?.pageSlug : "",
    step_product_id: product_name.value ? product_name.value : "",
    product_pricing_plan: price_plan.value ? price_plan.value : "",
    step_product_plan: price_plan.value ? price_plan.value : "",
    design_type: nodeStore.getNodeClickedData.design_type,
    funnel_id: nodeStore.getFunnelId,
    step_id: nodeStore.getNodeClickedData?.step_id,
  };
  const { error } = await updateStepNode(pageData, nodeId);
  saveBtnEle.value.buttonRef.disabled = false;
  saveBtnEle.value.buttonRef.removeAttribute("loader-indicator");
  if (error) return;
  nodeStore.closeNodeClickedModal();
});

const onSelectHandler = async (productId) => {
  productId
    ? (errorMessage.value = "")
    : (errorMessage.value = "product is required");
  pricePlans.value = null;
  const { response, error } = await allPricePlans(productId);
  if (!response) return;
  pricePlans.value = response;
};
const onSelectPlanHandler = async (event) => {
  event
    ? (errorPriceMessage.value = "")
    : (errorPriceMessage.value = "Price Plan is required");
};
</script>
<template>
  <Sidebar
    :class="[
      nodeStore.getNodeClickedModal &&
      checkNodeSlugType(nodeStore.getNodeClickedData.slug_type)
        ? 'open'
        : '',
    ]"
  >
    <template v-slot:s-header>
      <SidebarHeader>
        <template #header>
          <h5 class="title">{{ nodeStore.getNodeClickedData.title }}</h5>
        </template>
      </SidebarHeader>
    </template>
    <template v-slot:s-body>
      <div class="sidebar_body_container">
        <form @submit.prevent="updateNodeHandler" class="">
          <!-- for product default  -->
          <div v-if="nodeStore.getNodeClickedData.design_type === 'product'">
            <div class="select_prd_container mb-4">
              <label class="sub_title fw-500">Select Product</label>
              <Multiselect
                v-model="product_name"
                @select="onSelectHandler"
                :canDeselect="false"
                placeholder="Select Product"
                :options="nodeStore.getAllProductList"
                class="multi_select_input"
                :class="[errorMessage ? 'has_error' : '']"
              />
              <span class="error_message">{{ errorMessage }}</span>
            </div>
          </div>

          <!-- for use existing -->
          <div
            v-else-if="
              nodeStore.getNodeClickedData.design_type === 'product_page'
            "
          >
            <div class="mb-2 opt_label">
              <BaseInput
                v-model="nodeStore.getNodeClickedData.step_title"
                :name="'page'"
                label-name="Page Name"
              />
            </div>
            <div class="mb-2 opt_label">
              <BaseInput
                v-model="nodeStore.getNodeClickedData.step_slug"
                :name="'pageSlug'"
                label-name="Page Slug"
              />
            </div>
            <div class="select_prd_container mb-4">
              <label class="sub_title fw-500">Select Product</label>
              <Multiselect
                v-model="product_name"
                @select="onSelectHandler"
                :canDeselect="false"
                placeholder="Select Product"
                :options="nodeStore.getAllProductList"
                class="multi_select_input"
                :class="[errorMessage ? 'has_error' : '']"
              />
              <span class="error_message">{{ errorMessage }}</span>
            </div>
          </div>

          <!-- for create new with upsell downsell -->
          <div
            v-else-if="
              nodeStore.getNodeClickedData.design_type === 'product_page_price'
            "
            class=""
          >
            <div class="mb-2 opt_label">
              <BaseInput
                v-model="nodeStore.getNodeClickedData.step_title"
                :name="'page'"
                label-name="Page Name"
              />
            </div>
            <div class="select_prd_container mb-4">
              <label class="sub_title fw-500">Select Product </label>
              <Multiselect
                v-model="product_name"
                @select="onSelectHandler"
                :canDeselect="false"
                placeholder="Select Product"
                :options="nodeStore.getAllProductList"
                class="multi_select_input"
                :class="[errorMessage ? 'has_error' : '']"
              />
              <span class="error_message">{{ errorMessage }}</span>
            </div>
            <div class="select_prd_container mb-4">
              <label class="sub_title fw-500">Select pricing </label>
              <Multiselect
                v-model="price_plan"
                @select="onSelectPlanHandler"
                :canDeselect="false"
                placeholder="Select Pricing"
                :options="pricePlans"
                class="multi_select_input"
                :class="[errorPriceMessage ? 'has_error' : '']"
              />
              <span class="error_message">{{ errorPriceMessage }}</span>
            </div>
          </div>

          <!-- for Page -->
          <div v-else class="">
            <div class="mb-2 opt_label">
              <BaseInput
                v-model="nodeStore.getNodeClickedData.step_title"
                :name="'page'"
                label-name="Page Name"
              />
            </div>
            <div class="mb-2 opt_label">
              <BaseInput
                v-model="nodeStore.getNodeClickedData.step_slug"
                :name="'pageSlug'"
                label-name="Page Slug"
              />
            </div>
          </div>
          <div class="">
            <div class="d-flex align-items-center">
              <BaseButton ref="saveBtnEle" class="update_fix_btn" type="submit">
                <span
                  class="spinner-border spinner_c spinner-border-sm"
                  role="status"
                  aria-hidden="true"
                ></span>
                <span class="save__">update Node</span>
              </BaseButton>
            </div>
          </div>
        </form>
      </div>
    </template>
  </Sidebar>
</template>
<style lang="scss">
.v_page_link {
  text-decoration: none;
  color: #000;
  font-size: 16px;
  font-weight: 500;
  margin-left: 10px;
  cursor: pointer;
  text-align: center;
  padding: 10px 16px;

  &:hover {
    text-decoration: none;
    color: #000;
  }
}

.update_fix_btn {
  min-width: 149px;
  text-align: center;

  .spinner_c {
    margin: 0 auto;
  }
}

.sidebar_opt_body {
  padding: 28px 30px;
  width: 90%;
}
</style>
