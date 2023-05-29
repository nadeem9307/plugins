<script setup>
import { onMounted, onUpdated, reactive, ref } from "vue";
import Icon from "../Icons/Icon.vue";
import BaseButton from "../buttons/BaseButton.vue";
import RangeSelector from "../inputs/RangeSelector.vue";
import Sidebar from "./Sidebar.vue";
import SidebarHeader from "./SidebarHeader.vue";
import remove_btn from "@/assets/icons/minus_circle.svg";
import plus_btn from "@/assets/icons/plus_wh.svg";
import { useNodeStore } from "@/store/nodes";
import { getLastNode, getOnlyNumber } from "@/utils/Common";
import { getPercentageSpitData, saveFunnel, savePercentageSplits } from "@/api/funnel/funnel-service";
const nodeStore = useNodeStore();
let saveBtnEle = ref(null);
// const defaultData = reactive(nodeStore.getCustomPercentageArray);
let percentageList = reactive({ data: [] });
function getNextChar(char) {
  if (char === "z") {
    return "a";
  }
  if (char === "Z") {
    return "A";
  }

  return String.fromCharCode(char.charCodeAt(0) + 1);
}


onUpdated(async() => {
    const { step_id } = nodeStore.getActiveStep;
  if (step_id){
  const {responseData, error} = await getPercentageSpitData(step_id);
  const allVariations = responseData.variations?.map((item) => {
        return {
          value: "" + getOnlyNumber(item.value),
          label: item.label,
          disabled: item.disabled,
        };
      });
  percentageList.data = [...allVariations];
  }else{
    percentageList.data = [...nodeStore.getCustomPercentageArray];
  }
});
const addConditionHandler = () => {
  const newData = percentageList.data[percentageList.data.length - 1]["label"];
  const lastLetter = newData.split(" ").pop();
  percentageList.data.push({
    value: `50`,
    label: `Variant ${getNextChar(lastLetter)}`,
    disabled: "false",
  });
};

const removeCondition = (index) => {
  if (percentageList.data.length === 1) return;
  percentageList.data.splice(index, 1);
  const alpha = Array.from(Array(26)).map((e, i) => i + 65);
  const alphabet = alpha.map((x) => String.fromCharCode(x));
  percentageList.data.map((item, i) => (item.label = `Variant ${alphabet[i]}`));
};

const distributeHandler = () => {
  const equalValue = Math.trunc(100 / percentageList.data.length);
  percentageList.data.map((item, index) => {
    item.value = "" + equalValue;
  });
};

const removeHandler = () => {

};


const saveHandler = async () => {
  const slug_type =
    nodeStore.getActiveStep.slug_type == "percentage_split"
      ? "percentage_split"
      : null;
  const latestNode = getLastNode();
  const newArray = percentageList.data.map((item) => {
    return {
      label: item.label,
      value: `${item.label.split(" ").pop()} ${item.value}%`,
      disabled: "false",
    };
  });
  // nodeStore.updatePercentageArray(newArray);
  const percentageData = {
    funnel_id: nodeStore.getFunnelId,
    step_id: latestNode.id,
    variations: [...newArray],
  };
  if (saveBtnEle.value) {
    saveBtnEle.value.buttonRef.disabled = true;
    saveBtnEle.value.buttonRef.setAttribute("loader-indicator", "on");
  }
  const { error } = await savePercentageSplits(percentageData, slug_type);
  saveBtnEle.value.buttonRef.disabled = false;
  saveBtnEle.value.buttonRef.removeAttribute("loader-indicator");
  if (error) return;
  nodeStore.closeModal();
  await saveFunnel();
  const { data } = nodeStore.getNodeDetailsById(latestNode.id);
  nodeStore.updateActiveStepData(data);
};
</script>
<template>
  <Sidebar
    :class="[
      nodeStore.modalStatus &&
      nodeStore.getActiveStep.slug_type === 'percentage_split'
        ? 'open'
        : '',
    ]"
  >
    <template v-slot:s-header>
      <SidebarHeader>
        <template #header>
          <h5 class="title">Percentage Split</h5>
        </template>
      </SidebarHeader>
    </template>
    <template v-slot:s-body>
      <div class="sidebar_bodyy_container">
        <div class="percentage_container">
          <div
            class="percentage_wrapper"
            :key="index"
            v-for="(condition, index) in percentageList.data"
          >
            <div class="label_title">
              <h6>{{ condition.label }}</h6>
            </div>
            <div class="range_bar_box">
              <RangeSelector
                :min-value="0"
                :key="condition.index"
                :max-value="100"
                v-model="condition.value"
              />
            </div>
            <div class="range_value_box">
              <span>{{ condition.value.toString() }}</span> %
            </div>
            <div class="remove_icon">
              <button class="icon_btn btn" @click="removeCondition(index)">
                <Icon :name="remove_btn" class="rm_icon" />
              </button>
            </div>
          </div>
        </div>
        <div class="actions_container">
          <div
            class="add_new_condition d-flex align-items-center justify-content-between"
          >
            <BaseButton @on-click="addConditionHandler" class="btn_icon">
              <Icon :name="plus_btn" class="plus_icon" />
              <span class="ms-2">Add</span>
            </BaseButton>
            <BaseButton @on-click="distributeHandler" class="btn_outline">
              Distribute % evenly
            </BaseButton>
          </div>

          <div class="btm_actn_btn">
            <BaseButton  @on-click="removeHandler" class="btn_remove">
              remove
            </BaseButton>
            <BaseButton ref="saveBtnEle" @on-click="saveHandler">
              <span
                class="spinner-border spinner_c spinner-border-sm"
                role="status"
                aria-hidden="true"
              ></span>
              <span class="save__">save</span>
            </BaseButton>
          </div>
        </div>
      </div>
    </template>
  </Sidebar>
</template>
