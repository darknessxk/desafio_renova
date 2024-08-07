import {Flex, Modal, Form} from 'antd';
import {KeyOutlined, UserOutlined} from '@ant-design/icons';
import { Input } from 'antd';
import useRegister, {RegisterRequest} from "@/api/useRegister";
import {useCallback} from "react";

type RegisterFormType = RegisterRequest & {
    confirmPassword?: string;
};

type RegisterModalProps = {
    open: boolean;
    setOpen: (open: boolean) => void;
};

export default function RegisterModal({ open, setOpen }: RegisterModalProps) {
    const [form] = Form.useForm<RegisterFormType>();
    const {
        mutate: register,
        status
    } = useRegister({
        successCallback: () => {
            form.resetFields();
            setOpen(false);
        }
    });

    const onFinish = useCallback(async (values: RegisterFormType) => {
        register(values);
    }, [register])

    return <Modal
        open={open}
        title="Register"
        okText="Register"
        cancelText="Cancel"
        okButtonProps={{
            loading: status === 'pending'
        }}
        cancelButtonProps={{
            disabled: status === 'pending'
        }}
        onCancel={() => {
            form.resetFields()
            setOpen(false);
        }}
        onOk={() => {
            form.submit();
        }}
    >
        <Form form={form} preserve={false} onFinish={onFinish}>
            <Flex vertical gap={6}>
                <Form.Item<RegisterFormType>
                    name="firstName"
                    rules={[{ required: true, message: 'Please input your first name!' }]}
                >
                    <Input placeholder="First name" prefix={<UserOutlined />} />
                </Form.Item>

                <Form.Item<RegisterFormType>
                    name="lastName"
                    rules={[{ required: true, message: 'Please input your last name!' }]}
                >
                    <Input placeholder="Last Name" prefix={<UserOutlined />} />
                </Form.Item>

                <Form.Item<RegisterFormType>
                    name="email"
                    rules={[{ required: true, message: 'Please input your email!' }]}
                >
                    <Input placeholder="Email" prefix={<UserOutlined />} type="email" />
                </Form.Item>

                <Form.Item<RegisterFormType>
                    name="password"
                    rules={[ { required: true, message: 'Please input your password!' }, ]}
                >
                    <Input.Password placeholder="Password" prefix={<KeyOutlined />} />
                </Form.Item>

                <Form.Item<RegisterFormType>
                    name="confirmPassword"
                    rules={[
                        {
                            required: true,
                            message: 'Please confirm your password!',
                        },
                        ({ getFieldValue }) => ({
                            validator(_, value) {
                                if (!value || getFieldValue('password') === value) {
                                    return Promise.resolve();
                                }
                                return Promise.reject(new Error('The password that you entered do not match!'));
                            },
                        }),
                    ]}
                >
                    <Input.Password placeholder="Confirm Password" prefix={<KeyOutlined />} />
                </Form.Item>
            </Flex>
        </Form>
    </Modal>
}
