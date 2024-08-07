import {Modal, Form, InputNumber, Input} from 'antd';
import {useCallback, useMemo} from "react";
import {Project} from "@/types/Project";
import useNewDonation from "@/api/useNewDonation";

type NewDonationFormType = {
    origin?: string;
    value: number;
};

type NewDonationModalProps = {
    open: boolean;
    setOpen: (open: boolean) => void;
    project: Project;
};

export default function NewDonationModal({ open, setOpen, project }: NewDonationModalProps) {
    const [form] = Form.useForm<NewDonationFormType>();
    const maxDonation = useMemo(() => {
        return project.meta - project.projectDonationStatus.donationTotal;
    }, [ project ]);
    
    const {
        mutate: newDonation,
        status: newDonationStatus
    } = useNewDonation({ 
        id: project.id, 
        successCallback: () => {
            form.resetFields();
            setOpen(false);
        }
    })

    const onFinish = useCallback((values: NewDonationFormType) => {
        newDonation(values);
    }, [newDonation])

    return <Modal
        open={open}
        title="New Donation"
        okText="Donate"
        cancelText="Cancel"
        okButtonProps={{
            loading: newDonationStatus === 'pending'
        }}
        cancelButtonProps={{
            disabled: newDonationStatus === 'pending'
        }}
        onCancel={() => {
            form.resetFields()
            setOpen(false);
        }}
        onOk={() => { form.submit(); }}
    >
        <Form
            name={'login'}
            form={form}
            preserve={false}
            onFinish={onFinish}
            layout={"vertical"}
        >
            <Form.Item
                name={'origin'}
                label="Origin"
            >
                <Input style={{width: '100%'}} />
            </Form.Item>
            <Form.Item
                name={'value'}
                label="Value"
                rules={[{required: true, message: 'Please input the value!'}]}
            >
                <InputNumber
                    min={0}
                    max={maxDonation}
                    style={{ width: '100%' }}
                    placeholder="Meta"
                    formatter={(value) => `$ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
                    parser={(value) => value?.replace(/\$\s?|(,*)/g, '') as unknown as number}
                />
            </Form.Item>
        </Form>
    </Modal>
}