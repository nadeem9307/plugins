<script setup>
import { reactive, ref } from "vue";
import BaseButton from "../buttons/BaseButton.vue";
import BaseRadioInput from "../inputs/BaseRadioInput.vue";
import { useNodeStore } from "@/store/nodes";
const nodeStore = useNodeStore();
const props = defineProps({
  selectedItem: String,
  proceedArray: {
    type: Array,
  },
});
const emit = defineEmits(["onSubmit"]);
let selectedOption = ref(props.selectedItem);
const saveHandler = () => {
  emit("onSubmit", selectedOption.value);
};
</script>
<template>
  <div class="proceed_container">
    <div class="proceed_header">
      <h5>Proceed on:</h5>
    </div>
    <div class="proceed_body">
      <div class="radio_btn_wrap">
        <BaseRadioInput :options="proceedArray" v-model="selectedOption" />
      </div>
    </div>
    <div class="proceed_footer">
      <BaseButton
        class="save_btk"
        @on-click="saveHandler"
        :is-disabled="nodeStore.loadingStatus"
      >
        <span
          v-if="nodeStore.loadingStatus"
          class="spinner-border spinner-border-sm"
          role="status"
          aria-hidden="true"
        ></span>
        <span v-else class="save__">save</span>
      </BaseButton>
    </div>
  </div>
</template>
<style>
.save_btk {
  width: 90.6px;
}
</style>
