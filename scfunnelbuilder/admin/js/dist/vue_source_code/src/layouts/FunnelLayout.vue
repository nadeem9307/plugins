<script setup>
import StepsSidebar from "@/components/sidebar/StepsSidebar.vue";
import Layout from "./Layout.vue";
import { useNodeStore } from "@/store/nodes";
import { onMounted } from "vue";
import BaseApi from "@/api/BaseApi";
import { onUpdated } from "vue";
import { checkForStepType, getLastNode } from "@/utils/Common";
const nodeStore = useNodeStore();
// close right modal on body click
const modalCloseHandler = () => {
  nodeStore.closeNodeClickedModal();
  const { slug_type, step_id } = nodeStore.getActiveStep;
  if (checkForStepType(slug_type, ["percentage_split", "conditional_split"]) && step_id) {
    nodeStore.closeModal();
  }
};
</script>
<template>
  <Layout />
  <StepsSidebar />
  <div
    class="overlay"
    @click="modalCloseHandler"
    :class="[
      nodeStore.modalStatus || nodeStore.getNodeClickedModal
        ? 'active_overlay'
        : '',
    ]"
  ></div>
</template>
<style lang="scss">
.active_overlay {
  position: fixed;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
}
</style>
