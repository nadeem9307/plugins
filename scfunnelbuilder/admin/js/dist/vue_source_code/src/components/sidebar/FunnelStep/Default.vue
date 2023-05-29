<script setup>
import BaseSelect from "@/components/inputs/BaseSelect.vue";
import { addNewNode, allProducts } from "@/api/funnel/funnel-service";
import { useNodeStore } from "@/store/nodes";
import BaseButton from "@/components/buttons/BaseButton.vue";
import { ref, onMounted } from "vue";
import { useForm } from "vee-validate";
import { getLastNode } from "@/utils/Common";

const nodeStore = useNodeStore();
let saveBtnEle = ref(null);

const { handleSubmit } = useForm();
const createNodeHandler = handleSubmit(async (values) => {
  const latestNode = getLastNode();
  const newNode = {
    ...values,
    design_type: "default",
    funnel_id: nodeStore.getFunnelId,
    step_node_id: latestNode.id,
    slug_type: nodeStore.getActiveStep.slug_type,
    step_name: nodeStore.getActiveStep.label,
  };
  if (saveBtnEle.value) {
    saveBtnEle.value.buttonRef.disabled = true;
    saveBtnEle.value.buttonRef.setAttribute("loader-indicator", "on");
  }
  const { error, response } = await addNewNode(newNode);
  saveBtnEle.value.buttonRef.disabled = false;
  saveBtnEle.value.buttonRef.removeAttribute("loader-indicator");
  if (error) return;
  nodeStore.closeModal();

  //   await saveFunnel();
});
</script>
<template>
  <form @submit.prevent="createNodeHandler">
    <div class="mb-3">
      <div class="save_node_container">
        <div class="base_select_box">
          <BaseSelect
            :name="'productId'"
            :key="'default_1'"
            :optionsData="nodeStore.getAllProductList"
            label="Select Product"
            :placeholder="'Select Product'"
          />
        </div>
        <div class="">
          <BaseButton ref="saveBtnEle" class="save_node" type="submit">
            <span
              class="spinner-border spinner_c spinner-border-sm"
              role="status"
              aria-hidden="true"
            ></span>
            <span class="save__"> Save Node</span>
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
