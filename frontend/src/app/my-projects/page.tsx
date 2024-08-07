'use client';

import ProjectsList from "@/components/ProjectsList";
import {Button, Col} from "antd";
import {useUser} from "@/helper/user";
import {useState} from "react";
import NewProjectModal from "@/components/NewProjectModal";
import {useRouter} from "next/navigation";

export default function Page() {
    const { user } = useUser();
    const [openNewProjectModal, setOpenNewProjectModal] = useState(false)
    const router = useRouter()

    if (!user) {
        router.push('/')
    }

    return <>
        <ProjectsList
            headerContainer={<Col>
                <Button onClick={
                    () => setOpenNewProjectModal(true)
                }>New project</Button>
            </Col>}

        owner={user!.payload.id}
        />

        <NewProjectModal open={openNewProjectModal} setOpen={setOpenNewProjectModal} />
    </>
}
