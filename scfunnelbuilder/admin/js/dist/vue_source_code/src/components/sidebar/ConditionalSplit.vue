<script async setup>
import Multiselect from "@vueform/multiselect";
import Icon from "../Icons/Icon.vue";
import BaseButton from "../buttons/BaseButton.vue";
import Sidebar from "./Sidebar.vue";
import SidebarHeader from "./SidebarHeader.vue";
import remove_btn from "@/assets/icons/minus_circle.svg";
import plus_btn from "@/assets/icons/plus_wh.svg";
import { onMounted, onUpdated, reactive, ref } from "vue";
import { useNodeStore } from "@/store/nodes";
import {
  getConditionsData,
  getConfirmationList,
  saveConditions,
  saveFunnel,
} from "@/api/funnel/funnel-service";
import { getLastNode, getProductId } from "@/utils/Common";
import BaseApi from "@/api/BaseApi";
const nodeStore = useNodeStore();

const matchArr = ref(["AND", "OR"]);
const paramTwo = ref([
  { value: "plan", label: "Payment Plan" },
  { value: "bump", label: "Order Bump" },
  { value: "upsell", label: "Upsell" },
  { value: "downsell", label: "Downsell" },
]);
const paramOne = ref([
  { value: "ordered", label: "Contact ordered" },
  { value: "not-ordered", label: "Contact did not order" },
  { value: "field-value", label: "Field Value" },
]);
let resultArr = ref([{ value: "newany", label: "Any" }]);
// { value: 'newany', label: "Any" }
let saveBtnEle = ref(null);

let isLoaderVisible = ref(false);
let FieldValueParams = reactive([
  "First Name",
  "Email",
  "Country",
  "Address",
  "Address Line 2",
  "Town / City",
  "State / Country",
  "Postcode / Zip",
]);
let paramsConditionList = reactive([
  "is",
  "is not",
  "greater than",
  "less than",
  "contains",
]);

let condition_type = ref("AND");

let conditionsList = reactive({
  data: [
    {
      conditionCount: 3,
      newFieldValue: false,
      param_value_one: "ordered",
      param_value_two: "plan",
      result_condition: "newany",
      custom_file_value: "",
    },
  ],
});


const updateConditionData = async () => {
  const { slug_type, step_id } = nodeStore.getActiveStep;
  if (!step_id) return;
  const { error, responseData } = await getConditionsData(step_id);
  const { condition_type: condition_val, conditions } = responseData;
  const updatedConditionalArray = conditions?.map((item) => {
    
    if (!item.product_type) {
      return {
        conditionCount: 4,
        newFieldValue: true,
        param_value_one: item.action,
        param_value_two: item.cfield,
        result_condition: item.cfield_compare,
        custom_file_value: item.cfield_value,
      };
    }
    return {
      conditionCount: 3,
      newFieldValue: false,
      param_value_one: item.action,
      param_value_two: item.product_type,
      result_condition: item[item.product_type],
      custom_file_value: "",
    };
  });
  conditionsList.data = [...updatedConditionalArray];
  condition_type.value = condition_val;
  conditionsList.data?.forEach( async(arrayItem) => {
    const reqData = {
      product_type: arrayItem.param_value_two,
      productId: getProductId(),
    };
    if (reqData.product_type && reqData.productId){
      const { response } = await getConfirmationList(reqData);
      resultArr.value = response;
    }
});
};

const getResultCondition = () =>{
  // if (!reqData.product_type || !reqData.productId) return;
}

onMounted(async()=>{
  const reqData = {
    product_type: conditionsList.data?.[0].param_value_two,
    productId: getProductId(),
  };
  if (reqData.product_type && reqData.productId){
    const { response } = await getConfirmationList(reqData);
    resultArr.value = response;
  }
})

onUpdated(async () => {
  // const reqData = {
  //   product_type: "plan",
  //   productId: getProductId(),
  // };
  // // if (!reqData.product_type || !reqData.productId) return;
  // if (reqData.product_type && reqData.productId){
  //   const { response } = await getConfirmationList(reqData);
  //   resultArr.value = response;
  // }
  if (
    nodeStore.getActiveStep.slug_type &&
    nodeStore.getActiveStep.slug_type == "conditional_split"
  ) {
    updateConditionData();
  }

});

const addConditionHandler = () => {
  conditionsList.data.push({
    conditionCount: 3,
    newFieldValue: false,
    param_value_one: "ordered",
    param_value_two: "plan",
    result_condition: "newany",
    custom_file_value: "",
  });
};
const removeCondition = (index) => {
  if (conditionsList.data.length === 1) return;
  conditionsList.data.splice(index, 1);
};
const removeHandler = () => {
  // const { step_id } = nodeStore.getActiveStep;
  // if (!step_id) return;
  // nodeStore.deleteNode(step_id);
  // nodeStore.closeModal();
};



const saveHandler = async () => {
  const slug_type =
    nodeStore.getActiveStep.slug_type == "conditional_split"
      ? "conditional_split"
      : null;
  const latestNode = getLastNode();
  const updatedArray = conditionsList.data.map((item) => {
    return {
      action: item.param_value_one,
      product_type:
        item.param_value_one === "field-value" ? "" : item.param_value_two,
      cfield:
        item.param_value_one === "field-value" ? item.param_value_two : "",
      cfield_compare:
        item.param_value_one === "field-value" ? item.result_condition : "",
      cfield_value:
        item.param_value_one === "field-value" ? item.custom_file_value : "",
      plan: item.param_value_two === "plan" ? item.result_condition : "",
      bump: item.param_value_two === "bump" ? item.result_condition : "",
      upsell: item.param_value_two === "upsell" ? item.result_condition : "",
      downsell:
        item.param_value_two === "downsell" ? item.result_condition : "",
    };
  });
  if (!updatedArray.length) return;
  const conditionalData = {
    funnel_id: nodeStore.getFunnelId,
    step_node_id: latestNode.id,
    name: "",
    condition_type: condition_type.value ? condition_type.value : "",
    conditions: [...updatedArray],
    confirmation_type: "",
  };

  if (saveBtnEle.value) {
    saveBtnEle.value.buttonRef.disabled = true;
    saveBtnEle.value.buttonRef.setAttribute("loader-indicator", "on");
  }
  const { error } = await saveConditions(conditionalData, slug_type);
  saveBtnEle.value.buttonRef.disabled = false;
  saveBtnEle.value.buttonRef.removeAttribute("loader-indicator");
  if (error) return;
  nodeStore.closeModal();
  await saveFunnel();
  const { data } = nodeStore.getNodeDetailsById(latestNode.id);
  nodeStore.updateActiveStepData(data);
};


const paramsOneHandler = (index) => {
  conditionsList.data[index].conditionCount = 3;
  conditionsList.data[index].newFieldValue = false;
  if (conditionsList.data[index].param_value_one === "field-value") {
    conditionsList.data[index].conditionCount = 4;
    conditionsList.data[index].newFieldValue = true;
  }
};

const getConfirmationItems = async (index) => {
  conditionsList.data[index].result_condition=''
  const reqData = {
    product_type: conditionsList.data[index].param_value_two,
    productId: getProductId(),
  };
  if (!reqData.product_type || !reqData.productId) return;
  const { response } = await getConfirmationList(reqData);
  resultArr.value = response;
};
const newFieldParamValue = (index) =>{
  conditionsList.data[index].result_condition=''
}

const openHandler = () => {
  isLoaderVisible.value = true;
};
const closeHandler = () => {
  isLoaderVisible.value = false;
};
</script>
<template>
  <Sidebar
    :class="[
      nodeStore.modalStatus &&
      nodeStore.getActiveStep.slug_type === 'conditional_split'
        ? 'open'
        : '',
    ]"
  >
    <template v-slot:s-header>
      <SidebarHeader>
        <template #header>
          <div class="cond_header_wrapper">
            <div class="mb-3">
              <h4 class="title">Conditional split</h4>
            </div>
            <p>
              Your contact will join the 'Yes' path if they meet your
              conditions.
            </p>
            <div class="bot_section">
              <h5 class="">Match</h5>
              <div class="select_bdd">
                <div class="select_box_wrapper">
                  <Multiselect
                    v-model="condition_type"
                    :options="matchArr"
                    class="multi_select_input"
                  />
                </div>
              </div>
              <p>of the following conditions:</p>
            </div>
          </div>
        </template>
      </SidebarHeader>
    </template>
    <template v-slot:s-body>
      <div class="condition_ddd_wrapper">
        <div class="">
          <!-- all_condition_container -->
          <div
            class="conditions_wrapper"
            :key="index"
            v-for="(condition, index) in conditionsList.data"
          >
            <div class="custome_base_select select_condition">
              <a>
                {{ condition_type }}
              </a>
              <div class="select_box_wrapper">
                <Multiselect
                  v-model="condition.param_value_one"
                  :canDeselect="false"
                  @select="paramsOneHandler(index)"
                  :options="paramOne"
                  class="multi_select_input"
                />
              </div>
            </div>

            <template
              v-if="condition.conditionCount === 4 && condition.newFieldValue"
            >
              <div class="select_condition">
                <div class="select_box_wrapper">
                  <Multiselect
                    v-model="condition.param_value_two"
                    :canDeselect="false"
                    :options="FieldValueParams"
                    @select="newFieldParamValue(index)"
                    class="multi_select_input"
                  />
                </div>
              </div>

              <div class="select_condition">
                <div class="select_box_wrapper">
                  <Multiselect
                    v-model="condition.result_condition"
                    :canDeselect="false"
                    :options="paramsConditionList"
                    class="multi_select_input"
                  />
                </div>
              </div>
              <div class="select_condition">
                <div class="">
                  <input
                    v-model="condition.custom_file_value"
                    class="form-control base_input"
                  />
                </div>
              </div>
            </template>
            <!-- default select options -->
            <template
              v-if="condition.conditionCount === 3 && !condition.newFieldValue"
            >
              <div class="select_condition">
                <div class="select_box_wrapper">
                  <Multiselect
                    v-model="condition.param_value_two"
                    :canDeselect="false"
                    @select="getConfirmationItems(index)"
                    :options="paramTwo"
                    class="multi_select_input"
                  />
                </div>
              </div>

              <div class="select_condition">
                <div class="select_box_wrapper">
                  <Multiselect
                    v-model="condition.result_condition"
                    :canDeselect="false"
                    :loading="isLoaderVisible && !resultArr?.length"
                    @open="openHandler"
                    @close="closeHandler"
                    :options="resultArr"
                    class="multi_select_input"
                  />
                </div>
              </div>
            </template>
            <div class="remove_icon">
              <button class="icon_btn btn" @click="removeCondition(index)">
                <Icon :name="remove_btn" class="rm_icon" />
              </button>
            </div>
          </div>
          <div class="add_new_condition">
            <BaseButton @on-click="addConditionHandler" class="btn_icon">
              <Icon :name="plus_btn" class="plus_icon" />
              <span class="ms-2"> Add Another Condition</span>
            </BaseButton>
          </div>
        </div>
        <div class="btm_actn_btn">
          <!-- v-if="nodeStore.getActiveStep?.step_id" -->
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
    </template>
  </Sidebar>
</template>
<style lang="scss">
.add_new_condition {
  padding: 20px 0;

  .plus_icon {
    width: 20px;
    height: 20px;
  }
}

.btm_actn_btn {
  display: flex;
  align-items: center;
  justify-content: end;
  padding-top: 14px;
}
</style>
