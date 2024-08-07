"use client";

import {Project} from "@/types/Project";
import {Content} from "antd/lib/layout/layout";
import {Button, Divider, Flex, Popconfirm, Progress, theme} from "antd";
import {getLocale} from "@/helper/intl";
import Link from "antd/lib/typography/Link";
import {useRouter} from "next/navigation";
import EditProjectModal from "@/components/EditProjectModal";
import {useState} from "react";
import useDeleteProject from "@/api/useDeleteProject";
import NewDonationModal from "@/components/NewDonationModal";

export interface IProjectPreviewProps {
    project: Project;
    full?: boolean;
    edit?: boolean;
    setFilter?: (filters: Record<string, any>) => void;
}

export default function ProjectPreview({project, setFilter, full, edit}: IProjectPreviewProps) {
    const {
        token: { colorBgContainer, borderRadiusLG },
    } = theme.useToken();

    const intl = new Intl.NumberFormat(getLocale(), {
        style: "currency",
        currency: "USD",
    });

    const [openEditModal, setOpenEditModal] = useState(false);
    const {
        mutate: deleteProject,
        status: deleteStatus
    } = useDeleteProject();

    const router = useRouter();

    const [openDonationModal, setOpenDonationModal] = useState(false)

    return (
        <Content
            style={{
                padding: 24,
                margin: 10,
                minHeight: 80,
                background: colorBgContainer,
                borderRadius: borderRadiusLG,
            }}
        >
            <Flex vertical gap={3}>
                <h1 className={"text-xl font-semibold"}>{project.name}</h1>

                { full && (
                    <>
                        <Divider />
                        {project.description}
                        <Divider />
                    </>
                )}

                <Flex gap={4} justify={"space-between"}>
                    <p>Category:{" "}
                        <Link
                            href={`#`}
                            onClick={(e) => {
                                setFilter?.({category: project.category});
                            }}
                        >
                            {project.category[0].toUpperCase()}{project.category.slice(1)}
                        </Link>
                    </p>
                    <p>by{" "}
                        <Link
                            href={`#`}
                            onClick={(e) => {
                                setFilter?.({owner_id: project.owner.id});
                            }}
                        >
                            {project.owner.first_name}
                        </Link>
                    </p>
                </Flex>

                <Progress status={
                    project.projectDonationStatus.status === "completed" ? "success" : "active"
                } percent={project.projectDonationStatus.percentage} showInfo={false}/>

                <Flex justify={"space-between"}>
                    <p>
                        Total Donated: {intl.format(project.projectDonationStatus.donationTotal)}
                    </p>
                    <p>
                        Meta: {intl.format(project.meta)}
                    </p>
                </Flex>

                <Flex gap={6} justify={"flex-end"}>
                    {
                        !full && (
                            <Button
                                onClick={() => { router.push(`/project/${project.id}`) }} type={"primary"}
                                disabled={deleteStatus === 'pending'}
                            >View</Button>
                        )
                    }

                    {
                        (project.projectDonationStatus.status === "open" && full) && (
                            <>
                                <Button
                                    onClick={() => { setOpenDonationModal(true) }}
                                    disabled={deleteStatus === 'pending'}
                                    type={"primary"}
                                >Donate</Button>

                                <NewDonationModal project={project} open={openDonationModal} setOpen={setOpenDonationModal} />
                            </>
                        )
                    }

                    { edit && (
                        <>
                            <Button
                                onClick={() => { setOpenEditModal(true) }}
                                disabled={deleteStatus === 'pending'}
                                type={"primary"}
                            >Edit</Button>

                            <Popconfirm
                                title={"Are you sure?"}
                                onConfirm={() => { deleteProject(project.id.toString()) }}
                            >
                                <Button
                                    type={"primary"}
                                    loading={deleteStatus === 'pending'}
                                    danger
                                >Delete</Button>
                            </Popconfirm>

                            <EditProjectModal project={project} open={openEditModal} setOpen={setOpenEditModal} />
                        </>
                    )}
                </Flex>
            </Flex>
        </Content>
    )
}