<script setup>
import { useNodeStore } from "@/store/nodes";
import Icon from "../Icons/Icon.vue";
import BaseButton from "../buttons/BaseButton.vue";
import cancel from "@/assets/icons/cancel.svg";
import { checkForStepType, getLastNode } from "@/utils/Common";
const nodeStore = useNodeStore();
const props = defineProps({
  nodeData: {
    type: Object,
  },
});

const removeStepNodeHandler = () => {
  const { id } = getLastNode();
  if (!id) return;
  nodeStore.deleteNode(id);
  nodeStore.closeModal();
};
const closeVisible = () =>{
   checkForStepType(nodeStore.getActiveStep?.slug_type,['conditional_split','percentage_split'],true);
  const { step_id } = nodeStore.getActiveStep;
  if (!step_id) return;
  nodeStore.deleteNode(step_id);
  nodeStore.closeModal();
}
</script>

<template>
  <div class="s_header_wrap">
    <slot name="header"></slot>
    <BaseButton
      v-if="nodeStore.modalStatus&&!nodeStore.getActiveStep?.step_id "
      class="btn_flate"
      @on-click="removeStepNodeHandler"
    >
    <Icon :name="cancel" />
    </BaseButton>
  </div>
</template>
<style lang="scss">
.s_header_wrap {
  padding: 24px 30px;
  border-bottom: 1px solid #e8e8e8;
  display: flex;
  align-items: baseline;
  justify-content: space-between;

  h4 {
    text-transform: capitalize;
  }
}
</style>
